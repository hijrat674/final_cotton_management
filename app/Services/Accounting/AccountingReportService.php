<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Collection;

class AccountingReportService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function dashboardSummary(array $filters = []): array
    {
        $from = $filters['date_from'] ?? null;
        $to = $filters['date_to'] ?? null;

        $lines = JournalEntryLine::query()
            ->with(['account', 'journalEntry'])
            ->whereHas('journalEntry', fn ($query) => $query
                ->when($from, fn ($builder, $value) => $builder->whereDate('entry_date', '>=', $value))
                ->when($to, fn ($builder, $value) => $builder->whereDate('entry_date', '<=', $value)))
            ->get();

        $revenue = $lines->filter(fn (JournalEntryLine $line) => $line->account?->account_type === Account::TYPE_REVENUE)->sum('credit');
        $expenses = $lines->filter(fn (JournalEntryLine $line) => $line->account?->account_type === Account::TYPE_EXPENSE)->sum('debit');
        $cashBalance = $this->accountBalanceByCode(Account::CODE_CASH);
        $totalRevenue = round((float) $revenue, 2);
        $totalExpenses = round((float) $expenses, 2);

        return [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => round($totalRevenue - $totalExpenses, 2),
            'cash_balance' => $cashBalance,
            'cash_in' => round((float) $lines->filter(fn (JournalEntryLine $line) => $line->account?->code === Account::CODE_CASH)->sum('debit'), 2),
            'cash_out' => round((float) $lines->filter(fn (JournalEntryLine $line) => $line->account?->code === Account::CODE_CASH)->sum('credit'), 2),
            'monthly_profit' => $this->monthlyProfitTrend(),
            'revenue_vs_expense' => $this->revenueVsExpense($totalRevenue, $totalExpenses),
            'expense_distribution' => $this->expenseDistribution(),
            'cash_flow' => $this->cashFlowTrend(),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function profitAndLoss(array $filters = []): array
    {
        $lines = $this->filteredLines($filters);
        $revenueAccounts = $lines->filter(fn (JournalEntryLine $line) => $line->account?->account_type === Account::TYPE_REVENUE)
            ->groupBy('account_id')
            ->map(fn (Collection $group) => [
                'account' => $group->first()->account,
                'amount' => round((float) $group->sum('credit') - (float) $group->sum('debit'), 2),
            ])
            ->values();

        $expenseAccounts = $lines->filter(fn (JournalEntryLine $line) => $line->account?->account_type === Account::TYPE_EXPENSE)
            ->groupBy('account_id')
            ->map(fn (Collection $group) => [
                'account' => $group->first()->account,
                'amount' => round((float) $group->sum('debit') - (float) $group->sum('credit'), 2),
            ])
            ->values();

        $totalRevenue = round((float) $revenueAccounts->sum('amount'), 2);
        $totalExpenses = round((float) $expenseAccounts->sum('amount'), 2);

        return [
            'revenue_accounts' => $revenueAccounts,
            'expense_accounts' => $expenseAccounts,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => round($totalRevenue - $totalExpenses, 2),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function cashSummary(array $filters = []): array
    {
        $cashAccount = Account::query()->where('code', Account::CODE_CASH)->first();
        $lines = $this->filteredLines($filters)->filter(fn (JournalEntryLine $line) => $line->account_id === $cashAccount?->id);

        return [
            'cash_account' => $cashAccount,
            'total_cash_in' => round((float) $lines->sum('debit'), 2),
            'total_cash_out' => round((float) $lines->sum('credit'), 2),
            'cash_balance' => $cashAccount?->balance ?? 0,
            'transactions' => $lines->sortByDesc(fn (JournalEntryLine $line) => $line->journalEntry?->entry_date?->timestamp ?? 0)->values(),
        ];
    }

    public function customerReceivables(): array
    {
        $customers = Customer::query()
            ->withSum('sales', 'remaining_amount')
            ->withCount('sales')
            ->orderBy('full_name')
            ->get()
            ->filter(fn (Customer $customer) => $customer->outstanding_balance > 0)
            ->values();

        return [
            'customers' => $customers,
            'total_receivables' => round((float) $customers->sum('outstanding_balance'), 2),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function generalLedger(array $filters = []): array
    {
        $accounts = Account::query()
            ->with([
                'journalEntryLines' => fn ($query) => $query
                    ->with('journalEntry')
                    ->when($filters['date_from'] ?? null, fn ($builder, $value) => $builder->whereHas('journalEntry', fn ($entryQuery) => $entryQuery->whereDate('entry_date', '>=', $value)))
                    ->when($filters['date_to'] ?? null, fn ($builder, $value) => $builder->whereHas('journalEntry', fn ($entryQuery) => $entryQuery->whereDate('entry_date', '<=', $value))),
            ])
            ->when($filters['account_id'] ?? null, fn ($query, $accountId) => $query->where('id', $accountId))
            ->orderBy('code')
            ->get();

        return [
            'accounts' => $accounts,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function trialBalance(array $filters = []): array
    {
        $accounts = Account::query()
            ->with([
                'journalEntryLines' => fn ($query) => $query
                    ->when($filters['date_from'] ?? null, fn ($builder, $value) => $builder->whereHas('journalEntry', fn ($entryQuery) => $entryQuery->whereDate('entry_date', '>=', $value)))
                    ->when($filters['date_to'] ?? null, fn ($builder, $value) => $builder->whereHas('journalEntry', fn ($entryQuery) => $entryQuery->whereDate('entry_date', '<=', $value))),
            ])
            ->withSum('journalEntryLines', 'debit')
            ->withSum('journalEntryLines', 'credit')
            ->orderBy('code')
            ->get();

        return [
            'accounts' => $accounts,
            'total_debit' => round((float) $accounts->sum('journal_entry_lines_sum_debit'), 2),
            'total_credit' => round((float) $accounts->sum('journal_entry_lines_sum_credit'), 2),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, JournalEntryLine>
     */
    protected function filteredLines(array $filters): Collection
    {
        return JournalEntryLine::query()
            ->with(['account', 'journalEntry'])
            ->whereHas('journalEntry', fn ($query) => $query
                ->when($filters['date_from'] ?? null, fn ($builder, $value) => $builder->whereDate('entry_date', '>=', $value))
                ->when($filters['date_to'] ?? null, fn ($builder, $value) => $builder->whereDate('entry_date', '<=', $value)))
            ->get();
    }

    protected function accountBalanceByCode(string $code): float
    {
        $account = Account::query()
            ->withSum('journalEntryLines', 'debit')
            ->withSum('journalEntryLines', 'credit')
            ->where('code', $code)
            ->first();

        return $account?->balance ?? 0.0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function monthlyProfitTrend(): array
    {
        $months = JournalEntry::query()
            ->selectRaw('YEAR(entry_date) as entry_year, MONTH(entry_date) as entry_month')
            ->groupByRaw('YEAR(entry_date), MONTH(entry_date)')
            ->orderByRaw('YEAR(entry_date), MONTH(entry_date)')
            ->get();

        return $months->map(function ($month): array {
            $lines = JournalEntryLine::query()
                ->with('account')
                ->whereHas('journalEntry', fn ($query) => $query
                    ->whereYear('entry_date', $month->entry_year)
                    ->whereMonth('entry_date', $month->entry_month))
                ->get();

            $revenue = (float) $lines->filter(fn (JournalEntryLine $line) => $line->account?->account_type === Account::TYPE_REVENUE)->sum('credit');
            $expense = (float) $lines->filter(fn (JournalEntryLine $line) => $line->account?->account_type === Account::TYPE_EXPENSE)->sum('debit');

            return [
                'label' => sprintf('%04d-%02d', $month->entry_year, $month->entry_month),
                'profit' => round($revenue - $expense, 2),
            ];
        })->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function revenueVsExpense(float $revenue, float $expenses): array
    {
        return [
            ['label' => 'Revenue', 'amount' => round($revenue, 2)],
            ['label' => 'Expenses', 'amount' => round($expenses, 2)],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function expenseDistribution(): array
    {
        return Account::query()
            ->where('account_type', Account::TYPE_EXPENSE)
            ->withSum('journalEntryLines', 'debit')
            ->orderBy('code')
            ->get()
            ->map(fn (Account $account) => [
                'label' => $account->account_name,
                'amount' => round((float) $account->journal_entry_lines_sum_debit, 2),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function cashFlowTrend(): array
    {
        $cashAccount = Account::query()->where('code', Account::CODE_CASH)->first();

        if (! $cashAccount) {
            return [];
        }

        return JournalEntry::query()
            ->selectRaw('YEAR(entry_date) as entry_year, MONTH(entry_date) as entry_month')
            ->groupByRaw('YEAR(entry_date), MONTH(entry_date)')
            ->orderByRaw('YEAR(entry_date), MONTH(entry_date)')
            ->get()
            ->map(function ($month) use ($cashAccount): array {
                $lines = JournalEntryLine::query()
                    ->where('account_id', $cashAccount->id)
                    ->whereHas('journalEntry', fn ($query) => $query
                        ->whereYear('entry_date', $month->entry_year)
                        ->whereMonth('entry_date', $month->entry_month))
                    ->get();

                return [
                    'label' => sprintf('%04d-%02d', $month->entry_year, $month->entry_month),
                    'cash_in' => round((float) $lines->sum('debit'), 2),
                    'cash_out' => round((float) $lines->sum('credit'), 2),
                ];
            })
            ->all();
    }
}
