<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Services\Accounting\AccountingPostingService;

class ExpenseObserver
{
    public function __construct(
        protected AccountingPostingService $accountingPostingService
    ) {
    }

    public function saved(Expense $expense): void
    {
        $expense->loadMissing('expenseType');

        if ($expense->expenseType?->name === ExpenseType::NAME_SALARY) {
            return;
        }

        $this->accountingPostingService->deletePostingByReference(\App\Models\JournalEntry::REFERENCE_EXPENSE, $expense->id);

        if ($expense->production_stage_id) {
            $this->accountingPostingService->deletePostingByReference(\App\Models\JournalEntry::REFERENCE_PRODUCTION, (int) $expense->production_stage_id);
        }

        if ($expense->getOriginal('production_stage_id')) {
            $this->accountingPostingService->deletePostingByReference(\App\Models\JournalEntry::REFERENCE_PRODUCTION, (int) $expense->getOriginal('production_stage_id'));
        }

        $this->accountingPostingService->syncExpensePosting($expense);
    }

    public function deleted(Expense $expense): void
    {
        $expense->loadMissing('expenseType');

        if ($expense->expenseType?->name === ExpenseType::NAME_SALARY) {
            return;
        }

        $this->accountingPostingService->deleteExpensePosting($expense);
    }
}
