<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\SalaryPayment;
use App\Models\SalaryRecord;
use App\Services\Accounting\AccountingPostingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    public function __construct(
        protected AccountingPostingService $accountingPostingService
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createSalaryRecord(array $attributes, int $createdBy): SalaryRecord
    {
        return DB::transaction(function () use ($attributes, $createdBy): SalaryRecord {
            $employee = Employee::query()->findOrFail((int) $attributes['employee_id']);
            $bonus = round((float) ($attributes['bonus'] ?? 0), 2);
            $deduction = round((float) ($attributes['deduction'] ?? 0), 2);
            $basicSalary = round((float) $attributes['basic_salary'], 2);
            $grossSalary = round($basicSalary + $bonus - $deduction, 2);
            $pendingAdvances = EmployeeAdvance::query()
                ->where('employee_id', $employee->id)
                ->where('status', EmployeeAdvance::STATUS_PENDING)
                ->lockForUpdate()
                ->get();
            $advanceDeduction = round((float) $pendingAdvances->sum('amount'), 2);
            $totalSalary = round($grossSalary - $advanceDeduction, 2);

            if ($grossSalary < 0) {
                throw ValidationException::withMessages([
                    'deduction' => 'Deductions cannot reduce total salary below zero.',
                ]);
            }

            if ($totalSalary < 0) {
                throw ValidationException::withMessages([
                    'employee_id' => 'Pending advances exceed the gross salary for this period. Clear or adjust the advances before generating payroll.',
                ]);
            }

            $salaryRecord = SalaryRecord::create([
                'employee_id' => $employee->id,
                'salary_month' => $attributes['salary_month'],
                'salary_year' => $attributes['salary_year'],
                'basic_salary' => $basicSalary,
                'bonus' => $bonus,
                'deduction' => $deduction,
                'advance_deduction' => $advanceDeduction,
                'total_salary' => $totalSalary,
                'paid_amount' => 0,
                'remaining_amount' => max(0, $totalSalary),
                'payment_status' => $totalSalary <= 0 ? SalaryRecord::STATUS_PAID : SalaryRecord::STATUS_UNPAID,
                'notes' => $attributes['notes'] ?? null,
                'created_by' => $createdBy,
            ]);

            if ($pendingAdvances->isNotEmpty()) {
                EmployeeAdvance::query()
                    ->whereKey($pendingAdvances->pluck('id'))
                    ->update(['status' => EmployeeAdvance::STATUS_DEDUCTED]);
            }

            return $salaryRecord->load(['employee', 'creator', 'payments']);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function recordPayment(SalaryRecord $salaryRecord, array $attributes, int $receivedBy): SalaryPayment
    {
        return DB::transaction(function () use ($salaryRecord, $attributes, $receivedBy): SalaryPayment {
            $lockedRecord = SalaryRecord::query()->lockForUpdate()->findOrFail($salaryRecord->id);
            $lockedRecord->loadMissing('employee');

            if ((int) $lockedRecord->employee_id !== (int) $attributes['employee_id']) {
                throw ValidationException::withMessages([
                    'employee_id' => 'The selected employee does not match the salary record.',
                ]);
            }

            $amount = round((float) $attributes['amount'], 2);

            if ($amount > (float) $lockedRecord->remaining_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount cannot exceed the remaining salary balance.',
                ]);
            }

            $payment = $lockedRecord->payments()->create([
                'employee_id' => $attributes['employee_id'],
                'payment_date' => $attributes['payment_date'],
                'amount' => $amount,
                'payment_method' => $attributes['payment_method'],
                'received_by' => $receivedBy,
                'notes' => $attributes['notes'] ?? null,
            ]);

            $this->refreshSalarySummary($lockedRecord);
            $expense = $this->createSalaryExpense($lockedRecord, $payment, $receivedBy);
            $this->accountingPostingService->postSalaryPayment($payment->fresh(['employee', 'salaryRecord']), $expense);

            return $payment->load(['employee', 'receiver', 'salaryRecord']);
        });
    }

    public function refreshSalarySummary(SalaryRecord $salaryRecord): SalaryRecord
    {
        $paidAmount = round((float) $salaryRecord->payments()->sum('amount'), 2);
        $remainingAmount = round(max(0, (float) $salaryRecord->total_salary - $paidAmount), 2);
        $status = match (true) {
            $remainingAmount <= 0 => SalaryRecord::STATUS_PAID,
            $paidAmount > 0 => SalaryRecord::STATUS_PARTIAL,
            default => SalaryRecord::STATUS_UNPAID,
        };

        $salaryRecord->update([
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $status,
        ]);

        return $salaryRecord->refresh();
    }

    protected function createSalaryExpense(SalaryRecord $salaryRecord, SalaryPayment $payment, int $createdBy): Expense
    {
        $salaryExpenseTypeId = ExpenseType::query()
            ->where('name', ExpenseType::NAME_SALARY)
            ->value('id');

        if (! $salaryExpenseTypeId) {
            throw ValidationException::withMessages([
                'payroll' => 'The salary expense type is missing. Seed the expense types before posting payroll payments.',
            ]);
        }

        return Expense::create([
            'title' => 'Salary Payment - '.$salaryRecord->employee->full_name.' - '.$salaryRecord->period_label,
            'expense_type_id' => $salaryExpenseTypeId,
            'amount' => $payment->amount,
            'expense_date' => $payment->payment_date,
            'production_stage_id' => null,
            'employee_id' => $salaryRecord->employee_id,
            'created_by' => $createdBy,
            'notes' => $payment->notes ?: 'Auto-generated from salary payment #'.$payment->id.'.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createEmployeeAdvance(array $attributes, int $createdBy): EmployeeAdvance
    {
        return DB::transaction(function () use ($attributes, $createdBy): EmployeeAdvance {
            $employee = Employee::query()->findOrFail((int) $attributes['employee_id']);
            $amount = round((float) $attributes['amount'], 2);

            $advance = EmployeeAdvance::create([
                'employee_id' => $employee->id,
                'amount' => $amount,
                'advance_date' => $attributes['advance_date'],
                'reason' => $attributes['reason'] ?? null,
                'status' => EmployeeAdvance::STATUS_PENDING,
                'created_by' => $createdBy,
            ]);

            $this->accountingPostingService->postEmployeeAdvance($advance->fresh(['employee']));

            return $advance->load(['employee', 'creator']);
        });
    }
}
