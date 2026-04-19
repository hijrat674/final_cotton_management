<?php

namespace App\Services\Sales;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\Accounting\AccountingPostingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(
        protected AccountingPostingService $accountingPostingService
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createSale(array $attributes, int $soldBy): Sale
    {
        return DB::transaction(function () use ($attributes, $soldBy): Sale {
            $inventoryItems = $this->lockInventoryItems($attributes['items']);
            $preparedItems = $this->prepareItems($attributes['items'], $inventoryItems);
            $totals = $this->calculateTotals($preparedItems, (float) $attributes['paid_amount']);

            $sale = Sale::create([
                'customer_id' => $attributes['customer_id'],
                'sale_date' => $attributes['sale_date'],
                'total_amount' => $totals['total_amount'],
                'paid_amount' => $totals['paid_amount'],
                'remaining_amount' => $totals['remaining_amount'],
                'sold_by' => $soldBy,
                'notes' => $attributes['notes'] ?? null,
            ]);

            $this->syncSaleItems($sale, $preparedItems);
            $this->syncInventoryTransactions($sale, $preparedItems, $soldBy);
            $this->accountingPostingService->postSale($sale->refresh());

            return $sale->load(['customer', 'seller', 'items.inventoryItem', 'payments']);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateSale(Sale $sale, array $attributes): Sale
    {
        return DB::transaction(function () use ($sale, $attributes): Sale {
            $sale->loadMissing(['items', 'payments']);
            $inventoryItems = $this->lockInventoryItemsForUpdate($sale, $attributes['items']);
            $preparedItems = $this->prepareItems($attributes['items'], $inventoryItems, $sale);
            $totals = $this->calculateTotals($preparedItems, (float) $attributes['paid_amount']);

            if ($sale->payments->isNotEmpty() && round((float) $sale->paid_amount, 2) !== $totals['paid_amount']) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Paid amount cannot be edited after follow-up payments have been recorded.',
                ]);
            }

            $sale->update([
                'customer_id' => $attributes['customer_id'],
                'sale_date' => $attributes['sale_date'],
                'total_amount' => $totals['total_amount'],
                'paid_amount' => $totals['paid_amount'],
                'remaining_amount' => $totals['remaining_amount'],
                'notes' => $attributes['notes'] ?? null,
            ]);

            $sale->items()->delete();
            $this->deleteInventoryTransactions($sale);

            $this->syncSaleItems($sale, $preparedItems);
            $this->syncInventoryTransactions($sale, $preparedItems, $sale->sold_by);
            $this->accountingPostingService->postSale($sale->refresh());

            return $sale->load(['customer', 'seller', 'items.inventoryItem', 'payments']);
        });
    }

    public function deleteSale(Sale $sale): void
    {
        DB::transaction(function () use ($sale): void {
            $sale->loadMissing(['items', 'payments']);
            $this->lockInventoryItems($sale->items->map(fn ($item) => [
                'inventory_item_id' => $item->inventory_item_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])->all());

            if (! $sale->canBeDeletedSafely()) {
                throw ValidationException::withMessages([
                    'sale' => 'This sale cannot be deleted because payment has already been collected against the invoice.',
                ]);
            }

            $this->accountingPostingService->deleteSalePosting($sale);
            $this->deleteInventoryTransactions($sale);
            $sale->items()->delete();
            $sale->delete();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function recordPayment(Sale $sale, array $attributes, int $receivedBy): SalePayment
    {
        return DB::transaction(function () use ($sale, $attributes, $receivedBy): SalePayment {
            $lockedSale = Sale::query()->lockForUpdate()->findOrFail($sale->id);
            $existingPaymentTotal = round((float) $lockedSale->payments()->sum('amount'), 2);
            $initialPaidAmount = round((float) $lockedSale->paid_amount - $existingPaymentTotal, 2);
            $initialPaidAmount = max(0, $initialPaidAmount);

            if ((int) $lockedSale->customer_id !== (int) $attributes['customer_id']) {
                throw ValidationException::withMessages([
                    'customer_id' => 'The payment customer does not match the sale customer.',
                ]);
            }

            $amount = round((float) $attributes['amount'], 2);

            if ($amount > (float) $lockedSale->remaining_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount cannot exceed the remaining balance.',
                ]);
            }

            $payment = $lockedSale->payments()->create([
                'customer_id' => $attributes['customer_id'],
                'payment_date' => $attributes['payment_date'],
                'amount' => $amount,
                'payment_method' => $attributes['payment_method'],
                'received_by' => $receivedBy,
                'notes' => $attributes['notes'] ?? null,
            ]);

            $this->refreshPaymentSummary($lockedSale, $initialPaidAmount);
            $this->accountingPostingService->postSalePayment($payment->refresh());

            return $payment->load(['sale', 'customer', 'receiver']);
        });
    }

    public function refreshPaymentSummary(Sale $sale, ?float $initialPaidAmount = null): Sale
    {
        $followUpPaymentsTotal = round((float) $sale->payments()->sum('amount'), 2);
        $initialPaidAmount ??= round((float) $sale->paid_amount - $followUpPaymentsTotal, 2);
        $initialPaidAmount = max(0, $initialPaidAmount);
        $paidAmount = round($initialPaidAmount + $followUpPaymentsTotal, 2);
        $remainingAmount = round(max(0, (float) $sale->total_amount - $paidAmount), 2);

        $sale->update([
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
        ]);

        return $sale->refresh();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, InventoryItem>
     */
    protected function lockInventoryItems(array $items): Collection
    {
        $inventoryItemIds = collect($items)
            ->pluck('inventory_item_id')
            ->map(fn (mixed $id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        return InventoryItem::query()
            ->withStockSummary()
            ->whereIn('id', $inventoryItemIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, InventoryItem>
     */
    protected function lockInventoryItemsForUpdate(Sale $sale, array $items): Collection
    {
        $sale->loadMissing('items');

        $inventoryItemIds = collect($items)
            ->pluck('inventory_item_id')
            ->map(fn (mixed $id) => (int) $id)
            ->merge($sale->items->pluck('inventory_item_id')->map(fn (mixed $id) => (int) $id))
            ->filter()
            ->unique()
            ->values();

        return InventoryItem::query()
            ->withStockSummary()
            ->whereIn('id', $inventoryItemIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    protected function prepareItems(array $items, Collection $inventoryItems, ?Sale $sale = null): array
    {
        $existingQuantities = $sale?->items
            ->groupBy('inventory_item_id')
            ->map(fn (Collection $group) => round((float) $group->sum('quantity'), 3)) ?? collect();

        $runningTotals = [];
        $preparedItems = [];

        foreach ($items as $index => $item) {
            $inventoryItem = $inventoryItems->get((int) $item['inventory_item_id']);

            if (! $inventoryItem) {
                throw ValidationException::withMessages([
                    "items.$index.inventory_item_id" => 'The selected inventory item could not be resolved.',
                ]);
            }

            $quantity = round((float) $item['quantity'], 3);
            $unitPrice = round((float) $item['unit_price'], 2);
            $runningTotals[$inventoryItem->id] = ($runningTotals[$inventoryItem->id] ?? 0) + $quantity;
            $availableStock = $inventoryItem->current_stock + (float) ($existingQuantities[$inventoryItem->id] ?? 0);

            if ($runningTotals[$inventoryItem->id] > $availableStock) {
                throw ValidationException::withMessages([
                    "items.$index.quantity" => 'Sold quantity exceeds the available stock for '.$inventoryItem->product_name.'.',
                ]);
            }

            $preparedItems[] = [
                'inventory_item_id' => $inventoryItem->id,
                'product_name' => $inventoryItem->product_name,
                'quantity' => $quantity,
                'unit' => $inventoryItem->unit,
                'unit_price' => $unitPrice,
                'total_price' => round($quantity * $unitPrice, 2),
            ];
        }

        return $preparedItems;
    }

    /**
     * @param  array<int, array<string, mixed>>  $preparedItems
     * @return array<string, float>
     */
    protected function calculateTotals(array $preparedItems, float $paidAmount): array
    {
        $totalAmount = round(collect($preparedItems)->sum('total_price'), 2);
        $paidAmount = round($paidAmount, 2);

        if ($paidAmount > $totalAmount) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount cannot exceed the invoice total.',
            ]);
        }

        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'remaining_amount' => round($totalAmount - $paidAmount, 2),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $preparedItems
     */
    protected function syncSaleItems(Sale $sale, array $preparedItems): void
    {
        foreach ($preparedItems as $item) {
            $sale->items()->create([
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $preparedItems
     */
    protected function syncInventoryTransactions(Sale $sale, array $preparedItems, int $createdBy): void
    {
        foreach ($preparedItems as $item) {
            InventoryTransaction::create([
                'inventory_item_id' => $item['inventory_item_id'],
                'transaction_type' => InventoryTransaction::TYPE_SALE,
                'reference_type' => InventoryTransaction::REFERENCE_SALE,
                'reference_id' => $sale->id,
                'quantity_in' => 0,
                'quantity_out' => $item['quantity'],
                'transaction_date' => $sale->sale_date,
                'notes' => 'Inventory issued for sale #'.$sale->id.' - '.$item['product_name'].'.',
                'created_by' => $createdBy,
            ]);
        }
    }

    protected function deleteInventoryTransactions(Sale $sale): void
    {
        InventoryTransaction::query()
            ->where('reference_type', InventoryTransaction::REFERENCE_SALE)
            ->where('reference_id', $sale->id)
            ->delete();
    }
}
