<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\ProductionStage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'title' => (string) $request->string('title'),
            'expense_type_id' => (string) $request->string('expense_type_id'),
            'date_from' => (string) $request->string('date_from'),
            'date_to' => (string) $request->string('date_to'),
            'production_stage_id' => (string) $request->string('production_stage_id'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $query = Expense::query()
            ->with(['expenseType', 'productionStage', 'creator'])
            ->filterTitle($filters['title'])
            ->filterExpenseType($filters['expense_type_id'])
            ->filterDateRange($filters['date_from'], $filters['date_to'])
            ->filterProductionStage($filters['production_stage_id']);

        $summaryQuery = clone $query;

        $expenses = $query
            ->sortByExpenseDate($filters['sort'])
            ->paginate(12)
            ->withQueryString();

        return view('expenses.index', [
            'expenses' => $expenses,
            'filters' => $filters,
            'expenseTypes' => ExpenseType::query()->orderBy('id')->get(),
            'productionStages' => ProductionStage::query()->orderByDesc('stage_date')->orderByDesc('id')->get(),
            'summary' => [
                'total_expenses' => (clone $summaryQuery)->count(),
                'total_amount' => round((float) (clone $summaryQuery)->sum('amount'), 2),
                'production_linked_count' => (clone $summaryQuery)->whereNotNull('production_stage_id')->count(),
                'general_expenses_count' => (clone $summaryQuery)->whereNull('production_stage_id')->count(),
            ],
            'canCreateExpenses' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_PRODUCTION) ?? false,
            'canManageExpenses' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('expenses.create', $this->formData());
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $expense = Expense::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('expenses.show', $expense)
            ->with('status', 'Expense recorded successfully.');
    }

    public function show(Expense $expense): View
    {
        $expense->load(['expenseType', 'productionStage', 'creator']);

        return view('expenses.show', [
            'expense' => $expense,
            'canManageExpenses' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
            'canDeleteExpense' => $this->canDelete($expense),
        ]);
    }

    public function edit(Expense $expense): View|RedirectResponse
    {
        $expense->load(['expenseType', 'productionStage']);

        if ($this->isSystemManagedProductionExpense($expense)) {
            return redirect()
                ->route('expenses.show', $expense)
                ->withErrors([
                    'expense' => 'This production cost is system-managed by the linked production stage and cannot be edited from the expenses module.',
                ]);
        }

        return view('expenses.edit', $this->formData([
            'expense' => $expense,
        ]));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $expense->load('expenseType');

        if ($this->isSystemManagedProductionExpense($expense)) {
            return redirect()
                ->route('expenses.show', $expense)
                ->withErrors([
                    'expense' => 'This production cost is system-managed by the linked production stage and cannot be edited from the expenses module.',
                ]);
        }

        $expense->update($request->validated());

        return redirect()
            ->route('expenses.show', $expense)
            ->with('status', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->load('expenseType');

        if (! $this->canDelete($expense)) {
            return redirect()
                ->route('expenses.show', $expense)
                ->withErrors([
                    'expense' => 'This expense is system-managed by the linked production stage and cannot be deleted from the expenses module.',
                ]);
        }

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('status', 'Expense deleted successfully.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function formData(array $data = []): array
    {
        return [
            ...$data,
            'expenseTypes' => ExpenseType::query()->orderBy('id')->get(),
            'productionStages' => ProductionStage::query()->orderByDesc('stage_date')->orderByDesc('id')->get(),
            'selectedProductionStageId' => request()->integer('production_stage_id') ?: null,
        ];
    }

    protected function isSystemManagedProductionExpense(Expense $expense): bool
    {
        return $expense->production_stage_id !== null
            && $expense->expenseType?->name === ExpenseType::NAME_PRODUCTION;
    }

    protected function canDelete(Expense $expense): bool
    {
        return ! $this->isSystemManagedProductionExpense($expense);
    }
}
