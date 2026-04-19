<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CottonEntry;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\InventoryItem;
use App\Models\JournalEntryLine;
use App\Models\ProductionStage;
use App\Models\ProductionStageOutput;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalaryRecord;
use App\Services\Accounting\AccountingReportService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $inventorySummary = $this->inventorySummary();
        $cottonSummary = $this->cottonSummary();
        $productionSummary = $this->productionSummary();
        $expenseSummary = $this->expenseSummary();
        $salesSummary = $this->salesSummary();
        $accountingSummary = $this->accountingSummary();
        $employeeSummary = $this->employeeSummary();
        $payrollSummary = $this->payrollSummary();
        $advanceSummary = $this->advanceSummary();

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'totalUsers' => User::count(),
                'activeUsers' => User::query()->where('status', User::STATUS_ACTIVE)->count(),
                'inactiveUsers' => User::query()->where('status', User::STATUS_INACTIVE)->count(),
                'roleCounts' => collect(User::roleOptions())->mapWithKeys(
                    fn (string $label, string $role) => [$role => [
                        'label' => $label,
                        'count' => User::query()->where('role', $role)->count(),
                    ]]
                ),
                'recentUsers' => User::query()->latest()->take(8)->get(),
                'inventorySummary' => $inventorySummary,
                'cottonSummary' => $cottonSummary,
                'productionSummary' => $productionSummary,
                'expenseSummary' => $expenseSummary,
                'salesSummary' => $salesSummary,
                'accountingSummary' => $accountingSummary,
                'employeeSummary' => $employeeSummary,
                'payrollSummary' => $payrollSummary,
                'advanceSummary' => $advanceSummary,
            ]);
        }

        return match ($user->role) {
            User::ROLE_MANAGER => view('dashboard.manager', ['user' => $user, 'inventorySummary' => $inventorySummary, 'cottonSummary' => $cottonSummary, 'productionSummary' => $productionSummary, 'expenseSummary' => $expenseSummary, 'salesSummary' => $salesSummary, 'accountingSummary' => $accountingSummary, 'employeeSummary' => $employeeSummary, 'payrollSummary' => $payrollSummary, 'advanceSummary' => $advanceSummary]),
            User::ROLE_PRODUCTION => view('dashboard.production', ['user' => $user, 'inventorySummary' => $inventorySummary, 'cottonSummary' => $cottonSummary, 'productionSummary' => $productionSummary, 'expenseSummary' => $expenseSummary]),
            User::ROLE_SALES => view('dashboard.sales', ['user' => $user, 'salesSummary' => $salesSummary]),
            default => view('dashboard.manager', ['user' => $user, 'inventorySummary' => $inventorySummary, 'cottonSummary' => $cottonSummary, 'productionSummary' => $productionSummary, 'expenseSummary' => $expenseSummary, 'salesSummary' => $salesSummary, 'accountingSummary' => $accountingSummary, 'employeeSummary' => $employeeSummary, 'payrollSummary' => $payrollSummary, 'advanceSummary' => $advanceSummary]),
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function inventorySummary(): array
    {
        $items = InventoryItem::query()
            ->withStockSummary()
            ->orderBy('product_name')
            ->get();

        return [
            'total_items' => $items->count(),
            'total_stock_quantity' => round($items->sum('current_stock'), 3),
            'low_stock_count' => InventoryItem::lowStockCount($items),
            'out_of_stock_count' => InventoryItem::outOfStockCount($items),
            'items' => $items->map(fn (InventoryItem $item) => [
                'name' => $item->product_name,
                'quantity' => round($item->current_stock, 3),
                'formatted_quantity' => number_format($item->current_stock, 3, '.', ''),
                'unit' => $item->unit,
                'type' => $item->product_type,
            ])->all(),
            'product_distribution' => collect(InventoryItem::productTypeOptions())->mapWithKeys(
                fn (string $label, string $type) => [$type => [
                    'label' => $label,
                    'count' => $items->where('product_type', $type)->count(),
                ]]
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function cottonSummary(): array
    {
        $latestEntries = CottonEntry::query()
            ->with('inventoryItem')
            ->latest('entry_date')
            ->latest('id')
            ->take(5)
            ->get();

        return [
            'total_entries' => CottonEntry::count(),
            'today_entries' => CottonEntry::query()->whereDate('entry_date', today())->count(),
            'total_intake_quantity' => round((float) CottonEntry::query()->sum('net_weight'), 3),
            'latest_entries' => $latestEntries,
            'latest_entries_count' => $latestEntries->count(),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    protected function productionSummary(): array
    {
        return [
            'total_stages' => ProductionStage::count(),
            'today_production' => ProductionStage::query()->whereDate('stage_date', today())->count(),
            'total_input_quantity' => round((float) ProductionStage::query()->sum('input_quantity'), 3),
            'total_output_quantity' => round((float) ProductionStageOutput::query()->sum('quantity'), 3),
        ];
    }

    /**
     * @return array<string, float|int|array<int, array<string, mixed>>>
     */
    protected function expenseSummary(): array
    {
        $distribution = ExpenseType::query()
            ->withCount('expenses')
            ->orderBy('id')
            ->get()
            ->map(fn (ExpenseType $type) => [
                'name' => $type->name,
                'label' => ExpenseType::defaultOptions()[$type->name] ?? ucfirst($type->name),
                'count' => $type->expenses_count,
            ])
            ->all();

        return [
            'total_expenses_count' => Expense::count(),
            'total_expenses_amount' => round((float) Expense::query()->sum('amount'), 2),
            'today_expenses_amount' => round((float) Expense::query()->whereDate('expense_date', today())->sum('amount'), 2),
            'month_expenses_amount' => round((float) Expense::query()->whereBetween('expense_date', [today()->startOfMonth(), today()->endOfMonth()])->sum('amount'), 2),
            'distribution' => $distribution,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function salesSummary(): array
    {
        $dailySales = Sale::query()
            ->selectRaw('sale_date, SUM(total_amount) as total_amount')
            ->whereBetween('sale_date', [today()->subDays(6), today()])
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        $topProducts = SaleItem::query()
            ->selectRaw('inventory_item_id, SUM(quantity) as total_quantity')
            ->with('inventoryItem')
            ->groupBy('inventory_item_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        return [
            'total_sales_count' => Sale::count(),
            'today_sales' => Sale::query()->whereDate('sale_date', today())->count(),
            'total_revenue' => round((float) Sale::query()->sum('total_amount'), 2),
            'paid_amount' => round((float) Sale::query()->sum('paid_amount'), 2),
            'pending_amount' => round((float) Sale::query()->sum('remaining_amount'), 2),
            'open_invoices_count' => Sale::query()->where('remaining_amount', '>', 0)->count(),
            'daily_trend' => $dailySales->map(fn (Sale $sale) => [
                'date' => Carbon::parse($sale->sale_date)->format('M d'),
                'amount' => round((float) $sale->total_amount, 2),
            ])->all(),
            'top_products' => $topProducts->map(fn (SaleItem $item) => [
                'name' => $item->inventoryItem?->product_name ?? 'Unknown Item',
                'quantity' => round((float) $item->total_quantity, 3),
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function accountingSummary(): array
    {
        return app(AccountingReportService::class)->dashboardSummary();
    }

    /**
     * @return array<string, mixed>
     */
    protected function employeeSummary(): array
    {
        $employees = Employee::query()->get();

        return [
            'total_employees' => $employees->count(),
            'active_employees' => $employees->where('status', Employee::STATUS_ACTIVE)->count(),
            'inactive_employees' => $employees->where('status', Employee::STATUS_INACTIVE)->count(),
            'departments_count' => $employees->pluck('department')->filter()->unique()->count(),
            'department_distribution' => $employees->groupBy('department')->map(fn ($group, $department) => [
                'label' => $department,
                'count' => $group->count(),
            ])->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function payrollSummary(): array
    {
        $currentMonthRecords = SalaryRecord::query()
            ->with('employee')
            ->where('salary_month', now()->month)
            ->where('salary_year', now()->year)
            ->get();

        $monthlyTrend = SalaryRecord::query()
            ->selectRaw('salary_year, salary_month, SUM(paid_amount) as paid_total')
            ->groupBy('salary_year', 'salary_month')
            ->orderBy('salary_year')
            ->orderBy('salary_month')
            ->get()
            ->map(fn ($record) => [
                'label' => sprintf('%04d-%02d', $record->salary_year, $record->salary_month),
                'amount' => round((float) $record->paid_total, 2),
            ])
            ->all();

        $statusBreakdown = [
            ['label' => 'Paid', 'count' => SalaryRecord::query()->where('payment_status', SalaryRecord::STATUS_PAID)->count()],
            ['label' => 'Partial', 'count' => SalaryRecord::query()->where('payment_status', SalaryRecord::STATUS_PARTIAL)->count()],
            ['label' => 'Unpaid', 'count' => SalaryRecord::query()->where('payment_status', SalaryRecord::STATUS_UNPAID)->count()],
        ];

        $departmentDistribution = $currentMonthRecords
            ->groupBy(fn (SalaryRecord $record) => $record->employee->department)
            ->map(fn ($group, $department) => [
                'label' => $department,
                'amount' => round((float) $group->sum('paid_amount'), 2),
            ])
            ->values()
            ->all();

        return [
            'monthly_paid_total' => round((float) $currentMonthRecords->sum('paid_amount'), 2),
            'monthly_unpaid_total' => round((float) $currentMonthRecords->sum('remaining_amount'), 2),
            'monthly_employees_paid' => $currentMonthRecords->where('payment_status', SalaryRecord::STATUS_PAID)->count(),
            'monthly_salary_trend' => $monthlyTrend,
            'paid_vs_unpaid' => $statusBreakdown,
            'department_salary_distribution' => $departmentDistribution,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function advanceSummary(): array
    {
        $advances = EmployeeAdvance::query()->get();

        $monthlyTrend = EmployeeAdvance::query()
            ->selectRaw('YEAR(advance_date) as advance_year, MONTH(advance_date) as advance_month, SUM(amount) as advance_total')
            ->groupBy('advance_year', 'advance_month')
            ->orderBy('advance_year')
            ->orderBy('advance_month')
            ->get()
            ->map(fn ($advance) => [
                'label' => sprintf('%04d-%02d', $advance->advance_year, $advance->advance_month),
                'amount' => round((float) $advance->advance_total, 2),
            ])
            ->all();

        $advanceVsDeduction = SalaryRecord::query()
            ->selectRaw('salary_year, salary_month, SUM(advance_deduction) as deducted_total')
            ->groupBy('salary_year', 'salary_month')
            ->orderBy('salary_year')
            ->orderBy('salary_month')
            ->get()
            ->map(function ($salaryRecord) use ($monthlyTrend): array {
                $label = sprintf('%04d-%02d', $salaryRecord->salary_year, $salaryRecord->salary_month);
                $advanced = collect($monthlyTrend)->firstWhere('label', $label)['amount'] ?? 0;

                return [
                    'label' => $label,
                    'advanced' => round((float) $advanced, 2),
                    'deducted' => round((float) $salaryRecord->deducted_total, 2),
                ];
            })
            ->all();

        return [
            'total_advances' => round((float) $advances->sum('amount'), 2),
            'pending_advances' => round((float) $advances->where('status', EmployeeAdvance::STATUS_PENDING)->sum('amount'), 2),
            'deducted_advances' => round((float) $advances->where('status', EmployeeAdvance::STATUS_DEDUCTED)->sum('amount'), 2),
            'monthly_trend' => $monthlyTrend,
            'advance_vs_deduction' => $advanceVsDeduction,
        ];
    }
}
