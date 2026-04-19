<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\StoreProductionStageRequest;
use App\Http\Requests\Production\UpdateProductionStageRequest;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\ProductionStage;
use App\Models\ProductionStageOutput;
use App\Models\User;
use App\Services\Production\ProductionStageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionStageController extends Controller
{
    public function __construct(
        protected ProductionStageService $productionStageService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'stage_name' => (string) $request->string('stage_name'),
            'stage_date' => (string) $request->string('stage_date'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $stages = ProductionStage::query()
            ->with([
                'sourceInventoryItem',
                'handler',
                'handledByEmployee',
                'outputs.inventoryItem',
            ])
            ->withSum('outputs', 'quantity')
            ->filterStageName($filters['stage_name'])
            ->filterStageDate($filters['stage_date'])
            ->sortByStageDate($filters['sort'])
            ->paginate(10)
            ->withQueryString();

        return view('production_stages.index', [
            'stages' => $stages,
            'filters' => $filters,
            'canCreateStages' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_PRODUCTION) ?? false,
            'canManageStages' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('production_stages.create', [
            'inventoryItems' => InventoryItem::query()
                ->withStockSummary()
                ->orderBy('product_name')
                ->get(),
            'employees' => Employee::query()->active()->orderBy('full_name')->get(),
            'outputTypeOptions' => ProductionStageOutput::outputTypeOptions(),
        ]);
    }

    public function store(StoreProductionStageRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['handled_by'] = $request->user()->id;
        $validated['handled_by_employee_id'] = $request->integer('handled_by_employee_id');
        $validated['created_by'] = $request->user()->id;

        $stage = $this->productionStageService->createStage($validated);

        return redirect()
            ->route('production-stages.show', $stage)
            ->with('status', 'Production stage posted successfully. Inventory movements and production cost were recorded.');
    }

    public function show(ProductionStage $productionStage): View
    {
        $productionStage->load([
            'sourceInventoryItem',
            'handler',
            'handledByEmployee',
            'outputs.inventoryItem',
            'expenses.expenseType',
            'expenses.creator',
        ]);

        $inventoryMovements = InventoryTransaction::query()
            ->with(['inventoryItem', 'creator'])
            ->where('reference_type', InventoryTransaction::REFERENCE_PRODUCTION_STAGE)
            ->where('reference_id', $productionStage->id)
            ->sortByTransactionDate('oldest')
            ->get();

        return view('production_stages.show', [
            'stage' => $productionStage,
            'inventoryMovements' => $inventoryMovements,
            'hasDownstreamDependencies' => $this->productionStageService->hasDownstreamDependencies($productionStage),
            'canManageStages' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
            'productionExpense' => $productionStage->expenses
                ->first(fn ($expense) => $expense->expenseType?->name === \App\Models\ExpenseType::NAME_PRODUCTION),
        ]);
    }

    public function edit(ProductionStage $productionStage): View|RedirectResponse
    {
        $productionStage->load(['sourceInventoryItem', 'outputs.inventoryItem', 'expenses.expenseType']);

        if ($this->productionStageService->hasDownstreamDependencies($productionStage)) {
            return redirect()
                ->route('production-stages.show', $productionStage)
                ->withErrors([
                    'production_stage' => 'This production stage cannot be edited because one or more outputs are already used in downstream activity.',
                ]);
        }

        return view('production_stages.edit', [
            'stage' => $productionStage,
            'inventoryItems' => InventoryItem::query()
                ->withStockSummary()
                ->orderBy('product_name')
                ->get(),
            'employees' => Employee::query()
                ->where(function ($query) use ($productionStage) {
                    $query->active();

                    if ($productionStage->handled_by_employee_id) {
                        $query->orWhere('id', $productionStage->handled_by_employee_id);
                    }
                })
                ->orderBy('full_name')
                ->get(),
            'outputTypeOptions' => ProductionStageOutput::outputTypeOptions(),
        ]);
    }

    public function update(UpdateProductionStageRequest $request, ProductionStage $productionStage): RedirectResponse
    {
        $validated = $request->validated();
        $validated['handled_by'] = $productionStage->handled_by;
        $validated['handled_by_employee_id'] = $request->integer('handled_by_employee_id');
        $validated['created_by'] = $request->user()->id;

        $stage = $this->productionStageService->updateStage($productionStage, $validated);

        return redirect()
            ->route('production-stages.show', $stage)
            ->with('status', 'Production stage updated successfully. Inventory movements and production cost were synchronized.');
    }

    public function destroy(ProductionStage $productionStage): RedirectResponse
    {
        $this->productionStageService->deleteStage($productionStage);

        return redirect()
            ->route('production-stages.index')
            ->with('status', 'Production stage deleted successfully. Related inventory movements and cost entry were removed safely.');
    }
}
