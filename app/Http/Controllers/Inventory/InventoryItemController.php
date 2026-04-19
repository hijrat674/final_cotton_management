<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Inventory\UpdateInventoryItemRequest;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'product_name' => (string) $request->string('product_name'),
            'product_type' => (string) $request->string('product_type'),
            'unit' => (string) $request->string('unit'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $items = InventoryItem::query()
            ->withStockSummary()
            ->withCount('transactions')
            ->filterName($filters['product_name'])
            ->filterType($filters['product_type'])
            ->filterUnit($filters['unit'])
            ->sortByCreated($filters['sort'])
            ->paginate(10)
            ->withQueryString();

        $summaryItems = InventoryItem::query()->withStockSummary()->get();

        return view('inventory_items.index', [
            'items' => $items,
            'filters' => $filters,
            'productTypes' => InventoryItem::productTypeOptions(),
            'units' => InventoryItem::unitOptions(),
            'canManageItems' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
            'summary' => [
                'total_items' => $summaryItems->count(),
                'total_stock_quantity' => round($summaryItems->sum('current_stock'), 3),
                'low_stock_count' => InventoryItem::lowStockCount($summaryItems),
                'out_of_stock_count' => InventoryItem::outOfStockCount($summaryItems),
            ],
            'summaryItems' => $summaryItems
                ->sortBy('product_name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values(),
        ]);
    }

    public function create(): View
    {
        return view('inventory_items.create', [
            'productTypes' => InventoryItem::productTypeOptions(),
            'units' => InventoryItem::unitOptions(),
        ]);
    }

    public function store(StoreInventoryItemRequest $request): RedirectResponse
    {
        $item = InventoryItem::create($request->validated());

        return redirect()
            ->route('inventory-items.show', $item)
            ->with('status', 'Inventory item created successfully.');
    }

    public function show(InventoryItem $inventoryItem): View
    {
        $inventoryItem->load([
            'transactions' => fn ($query) => $query
                ->with('creator')
                ->sortByTransactionDate()
                ->take(10),
        ]);

        $inventoryItem->loadSum('transactions as total_quantity_in', 'quantity_in');
        $inventoryItem->loadSum('transactions as total_quantity_out', 'quantity_out');

        return view('inventory_items.show', [
            'item' => $inventoryItem,
            'canManageItems' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function edit(InventoryItem $inventoryItem): View
    {
        return view('inventory_items.edit', [
            'item' => $inventoryItem,
            'productTypes' => InventoryItem::productTypeOptions(),
            'units' => InventoryItem::unitOptions(),
        ]);
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $inventoryItem->update($request->validated());

        return redirect()
            ->route('inventory-items.show', $inventoryItem)
            ->with('status', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        if ($inventoryItem->hasTransactions()) {
            return back()->withErrors([
                'inventory_item' => 'This item cannot be deleted because stock transactions already exist for it.',
            ]);
        }

        $inventoryItem->delete();

        return redirect()
            ->route('inventory-items.index')
            ->with('status', 'Inventory item deleted successfully.');
    }
}
