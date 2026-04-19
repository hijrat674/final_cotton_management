<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryTransactionRequest;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'transaction_type' => (string) $request->string('transaction_type'),
            'inventory_item_id' => (string) $request->string('inventory_item_id'),
            'date_from' => (string) $request->string('date_from'),
            'date_to' => (string) $request->string('date_to'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $transactions = InventoryTransaction::query()
            ->with(['inventoryItem', 'creator'])
            ->filterType($filters['transaction_type'])
            ->filterItem($filters['inventory_item_id'])
            ->filterDateRange($filters['date_from'], $filters['date_to'])
            ->sortByTransactionDate($filters['sort'])
            ->paginate(12)
            ->withQueryString();

        return view('inventory_transactions.index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'transactionTypes' => InventoryTransaction::transactionTypeOptions(),
            'items' => InventoryItem::query()->orderBy('product_name')->get(),
            'canCreateTransactions' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_PRODUCTION) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('inventory_transactions.create', [
            'items' => InventoryItem::query()->withStockSummary()->orderBy('product_name')->get(),
            'transactionTypes' => InventoryTransaction::transactionTypeOptions(),
            'referenceTypes' => InventoryTransaction::referenceTypeOptions(),
            'selectedItemId' => request()->integer('inventory_item_id') ?: null,
        ]);
    }

    public function store(StoreInventoryTransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;
        $validated['quantity_in'] = $validated['quantity_in'] ?? 0;
        $validated['quantity_out'] = $validated['quantity_out'] ?? 0;

        $transaction = InventoryTransaction::create($validated);

        return redirect()
            ->route('inventory-transactions.show', $transaction)
            ->with('status', 'Inventory transaction recorded successfully.');
    }

    public function show(InventoryTransaction $inventoryTransaction): View
    {
        $inventoryTransaction->load(['inventoryItem', 'creator']);
        $inventoryTransaction->inventoryItem->loadSum('transactions as total_quantity_in', 'quantity_in');
        $inventoryTransaction->inventoryItem->loadSum('transactions as total_quantity_out', 'quantity_out');

        return view('inventory_transactions.show', [
            'transaction' => $inventoryTransaction,
            'canDeleteTransactions' => false,
        ]);
    }
}
