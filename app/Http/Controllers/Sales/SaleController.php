<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSaleRequest;
use App\Http\Requests\Sales\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\User;
use App\Services\Sales\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'customer_id' => (string) $request->string('customer_id'),
            'date_from' => (string) $request->string('date_from'),
            'date_to' => (string) $request->string('date_to'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $query = Sale::query()
            ->with(['customer', 'seller'])
            ->withCount('payments')
            ->filterCustomer($filters['customer_id'])
            ->filterDateRange($filters['date_from'], $filters['date_to']);

        $summaryQuery = clone $query;

        return view('sales.index', [
            'sales' => $query->sortBySaleDate($filters['sort'])->paginate(12)->withQueryString(),
            'filters' => $filters,
            'customers' => Customer::query()->orderBy('full_name')->get(),
            'summary' => [
                'total_sales' => (clone $summaryQuery)->count(),
                'total_revenue' => round((float) (clone $summaryQuery)->sum('total_amount'), 2),
                'paid_amount' => round((float) (clone $summaryQuery)->sum('paid_amount'), 2),
                'pending_amount' => round((float) (clone $summaryQuery)->sum('remaining_amount'), 2),
            ],
            'canCreateSales' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false,
            'canManageSales' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false,
            'canDeleteSales' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('sales.create', $this->formData());
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = $this->saleService->createSale($request->validated(), $request->user()->id);

        return redirect()
            ->route('sales.show', $sale)
            ->with('status', 'Sale recorded successfully. Stock was deducted from inventory.');
    }

    public function show(Sale $sale): View
    {
        $sale->load([
            'customer',
            'seller',
            'items.inventoryItem',
            'payments.receiver',
        ]);

        $inventoryMovements = InventoryTransaction::query()
            ->with(['inventoryItem', 'creator'])
            ->where('reference_type', InventoryTransaction::REFERENCE_SALE)
            ->where('reference_id', $sale->id)
            ->sortByTransactionDate('oldest')
            ->get();

        return view('sales.show', [
            'sale' => $sale,
            'inventoryMovements' => $inventoryMovements,
            'canManageSales' => auth()->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false,
            'canDeleteSales' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
            'canCollectPayments' => auth()->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false,
            'canDeleteSale' => $sale->canBeDeletedSafely(),
        ]);
    }

    public function edit(Sale $sale): View
    {
        $sale->load(['customer', 'items.inventoryItem', 'payments']);

        return view('sales.edit', $this->formData([
            'sale' => $sale,
        ]));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $sale = $this->saleService->updateSale($sale, $request->validated());

        return redirect()
            ->route('sales.show', $sale)
            ->with('status', 'Sale updated successfully. Inventory movements were synchronized.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->saleService->deleteSale($sale);

        return redirect()
            ->route('sales.index')
            ->with('status', 'Sale deleted successfully and linked stock was restored.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function formData(array $data = []): array
    {
        return [
            ...$data,
            'customers' => Customer::query()->orderBy('full_name')->get(),
            'inventoryItems' => InventoryItem::query()
                ->withStockSummary()
                ->orderBy('product_name')
                ->get(),
        ];
    }
}
