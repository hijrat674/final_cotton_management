<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\JournalEntry;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SalaryPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountingPostingService
{
    public function postEmployeeAdvance(EmployeeAdvance $advance): JournalEntry
    {
        $advance->loadMissing('employee');

        return $this->upsertEntry(
            entryDate: $advance->advance_date->toDateString(),
            referenceType: JournalEntry::REFERENCE_EMPLOYEE_ADVANCE,
            referenceId: $advance->id,
            description: 'Employee advance for '.$advance->employee->full_name.'.',
            createdBy: $advance->created_by,
            lines: [
                $this->line(Account::CODE_EMPLOYEE_ADVANCE, (float) $advance->amount, 0, 'Advance issued to employee.'),
                $this->line(Account::CODE_CASH, 0, (float) $advance->amount, 'Cash paid for employee advance.'),
            ]
        );
    }

    public function postSale(Sale $sale): JournalEntry
    {
        return $this->upsertEntry(
            entryDate: $sale->sale_date->toDateString(),
            referenceType: JournalEntry::REFERENCE_SALE,
            referenceId: $sale->id,
            description: 'Auto-posted sale invoice #'.$sale->id.'.',
            createdBy: $sale->sold_by,
            lines: array_filter([
                $this->line(Account::CODE_CASH, (float) $sale->paid_amount, 0, 'Cash received with sale.'),
                $this->line(Account::CODE_ACCOUNTS_RECEIVABLE, (float) $sale->remaining_amount, 0, 'Outstanding receivable for sale.'),
                $this->line(Account::CODE_SALES_REVENUE, 0, (float) $sale->total_amount, 'Sales revenue recognized.'),
            ], fn (array $line) => (float) $line['debit'] > 0 || (float) $line['credit'] > 0)
        );
    }

    public function deleteSalePosting(Sale $sale): void
    {
        JournalEntry::query()
            ->where('reference_type', JournalEntry::REFERENCE_SALE)
            ->where('reference_id', $sale->id)
            ->delete();
    }

    public function postSalePayment(SalePayment $payment): JournalEntry
    {
        $payment->loadMissing('sale');

        return $this->upsertEntry(
            entryDate: $payment->payment_date->toDateString(),
            referenceType: JournalEntry::REFERENCE_PAYMENT,
            referenceId: $payment->id,
            description: 'Auto-posted customer payment #'.$payment->id.' for sale #'.$payment->sale_id.'.',
            createdBy: $payment->received_by,
            lines: [
                $this->line(Account::CODE_CASH, (float) $payment->amount, 0, 'Cash received from customer payment.'),
                $this->line(Account::CODE_ACCOUNTS_RECEIVABLE, 0, (float) $payment->amount, 'Receivable settled by customer payment.'),
            ]
        );
    }

    public function syncExpensePosting(Expense $expense): JournalEntry
    {
        $expense->loadMissing('expenseType');

        $isProductionExpense = $expense->production_stage_id !== null
            && $expense->expenseType?->name === ExpenseType::NAME_PRODUCTION;

        $referenceType = $isProductionExpense
            ? JournalEntry::REFERENCE_PRODUCTION
            : JournalEntry::REFERENCE_EXPENSE;

        $referenceId = $isProductionExpense
            ? (int) $expense->production_stage_id
            : $expense->id;

        $expenseAccountCode = $isProductionExpense
            ? Account::CODE_PRODUCTION_EXPENSE
            : Account::CODE_GENERAL_EXPENSE;

        return $this->upsertEntry(
            entryDate: $expense->expense_date->toDateString(),
            referenceType: $referenceType,
            referenceId: $referenceId,
            description: $isProductionExpense
                ? 'Auto-posted production cost for stage #'.$expense->production_stage_id.'.'
                : 'Auto-posted expense #'.$expense->id.'.',
            createdBy: $expense->created_by,
            lines: [
                $this->line($expenseAccountCode, (float) $expense->amount, 0, $expense->title),
                $this->line(Account::CODE_CASH, 0, (float) $expense->amount, 'Cash paid for expense.'),
            ]
        );
    }

    public function postSalaryPayment(SalaryPayment $payment, Expense $expense): JournalEntry
    {
        $payment->loadMissing(['employee', 'salaryRecord']);
        $advanceAllocation = $this->salaryAdvanceAllocationForPayment($payment);
        $salaryExpenseAmount = round((float) $payment->amount + $advanceAllocation, 2);

        return $this->upsertEntry(
            entryDate: $payment->payment_date->toDateString(),
            referenceType: JournalEntry::REFERENCE_SALARY_PAYMENT,
            referenceId: $payment->id,
            description: 'Salary payment for '.$payment->employee->full_name.' - '.$payment->salaryRecord->period_label.'.',
            createdBy: $payment->received_by,
            lines: array_values(array_filter([
                $this->line(Account::CODE_SALARY_EXPENSE, $salaryExpenseAmount, 0, $expense->title),
                $this->line(Account::CODE_EMPLOYEE_ADVANCE, 0, $advanceAllocation, 'Advance deduction settled through payroll.'),
                $this->line(Account::CODE_CASH, 0, (float) $payment->amount, 'Cash paid for salary payment.'),
            ], fn (array $line) => (float) $line['debit'] > 0 || (float) $line['credit'] > 0))
        );
    }

    public function deleteExpensePosting(Expense $expense): void
    {
        $expense->loadMissing('expenseType');

        $isProductionExpense = $expense->production_stage_id !== null
            && $expense->expenseType?->name === ExpenseType::NAME_PRODUCTION;

        JournalEntry::query()
            ->where('reference_type', $isProductionExpense ? JournalEntry::REFERENCE_PRODUCTION : JournalEntry::REFERENCE_EXPENSE)
            ->where('reference_id', $isProductionExpense ? (int) $expense->production_stage_id : $expense->id)
            ->delete();
    }

    public function deletePostingByReference(string $referenceType, int $referenceId): void
    {
        JournalEntry::query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    protected function upsertEntry(
        string $entryDate,
        string $referenceType,
        int $referenceId,
        string $description,
        int $createdBy,
        array $lines
    ): JournalEntry {
        return DB::transaction(function () use ($entryDate, $referenceType, $referenceId, $description, $createdBy, $lines): JournalEntry {
            $validatedLines = $this->resolveLines($lines);
            $totalDebit = round(collect($validatedLines)->sum('debit'), 2);
            $totalCredit = round(collect($validatedLines)->sum('credit'), 2);

            if ($totalDebit !== $totalCredit) {
                throw ValidationException::withMessages([
                    'accounting' => 'Automatic journal entry is out of balance and could not be posted.',
                ]);
            }

            $entry = JournalEntry::query()->updateOrCreate(
                [
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                ],
                [
                    'entry_date' => $entryDate,
                    'description' => $description,
                    'created_by' => $createdBy,
                ]
            );

            $entry->lines()->delete();

            foreach ($validatedLines as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'],
                ]);
            }

            return $entry->load(['lines.account', 'creator']);
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    protected function resolveLines(array $lines): array
    {
        $accounts = Account::query()
            ->whereIn('code', collect($lines)->pluck('account_code')->all())
            ->get()
            ->keyBy('code');

        return collect($lines)
            ->map(function (array $line) use ($accounts): array {
                $account = $accounts->get($line['account_code']);

                if (! $account) {
                    throw ValidationException::withMessages([
                        'accounting' => 'Default accounting setup is incomplete. Please seed the chart of accounts.',
                    ]);
                }

                return [
                    'account_id' => $account->id,
                    'debit' => round((float) $line['debit'], 2),
                    'credit' => round((float) $line['credit'], 2),
                    'description' => $line['description'] ?? null,
                ];
            })
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function line(string $accountCode, float $debit, float $credit, ?string $description = null): array
    {
        return [
            'account_code' => $accountCode,
            'debit' => round($debit, 2),
            'credit' => round($credit, 2),
            'description' => $description,
        ];
    }

    protected function salaryAdvanceAllocationForPayment(SalaryPayment $payment): float
    {
        $salaryRecord = $payment->salaryRecord;
        $advanceTotal = round((float) $salaryRecord->advance_deduction, 2);
        $netPayable = round((float) $salaryRecord->total_salary, 2);

        if ($advanceTotal <= 0 || $netPayable <= 0) {
            return 0.0;
        }

        $previousCashPayments = round((float) SalaryPayment::query()
            ->where('salary_record_id', $salaryRecord->id)
            ->where('id', '<', $payment->id)
            ->sum('amount'), 2);

        $currentCashPayments = round($previousCashPayments + (float) $payment->amount, 2);

        $advanceBefore = $this->proportionalAdvanceAllocation($previousCashPayments, $netPayable, $advanceTotal);
        $advanceAfter = $this->proportionalAdvanceAllocation($currentCashPayments, $netPayable, $advanceTotal);

        return round(max(0, $advanceAfter - $advanceBefore), 2);
    }

    protected function proportionalAdvanceAllocation(float $cashPaid, float $netPayable, float $advanceTotal): float
    {
        if ($cashPaid <= 0 || $netPayable <= 0 || $advanceTotal <= 0) {
            return 0.0;
        }

        if ($cashPaid >= $netPayable) {
            return round($advanceTotal, 2);
        }

        return round(($cashPaid / $netPayable) * $advanceTotal, 2);
    }
}
