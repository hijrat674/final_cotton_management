<?php

namespace App\Services\Cotton;

use App\Models\CottonEntry;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CottonEntryInventoryService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createEntryWithTransaction(array $attributes): CottonEntry
    {
        return DB::transaction(function () use ($attributes): CottonEntry {
            $entry = CottonEntry::create($attributes);

            InventoryTransaction::create([
                'inventory_item_id' => $entry->inventory_item_id,
                'transaction_type' => InventoryTransaction::TYPE_INTAKE,
                'reference_type' => InventoryTransaction::REFERENCE_COTTON_ENTRY,
                'reference_id' => $entry->id,
                'quantity_in' => $entry->net_weight,
                'quantity_out' => 0,
                'transaction_date' => $entry->entry_date,
                'notes' => $entry->notes ?: 'Inventory intake created from cotton entry #'.$entry->id.'.',
                'created_by' => $entry->created_by,
            ]);

            return $entry;
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateEntryWithTransaction(CottonEntry $entry, array $attributes): CottonEntry
    {
        $transaction = $this->resolveLinkedTransaction($entry);
        $newNetWeight = (float) $attributes['net_weight'];
        $newInventoryItemId = (int) $attributes['inventory_item_id'];

        $this->guardSafeUpdate($entry, $transaction, $newNetWeight, $newInventoryItemId);

        return DB::transaction(function () use ($entry, $transaction, $attributes): CottonEntry {
            $entry->update($attributes);

            $transaction->update([
                'inventory_item_id' => $entry->inventory_item_id,
                'quantity_in' => $entry->net_weight,
                'quantity_out' => 0,
                'transaction_date' => $entry->entry_date,
                'notes' => $entry->notes ?: 'Inventory intake updated from cotton entry #'.$entry->id.'.',
            ]);

            return $entry->refresh();
        });
    }

    public function deleteEntryWithTransaction(CottonEntry $entry): void
    {
        $transaction = $this->resolveLinkedTransaction($entry);

        if (! $entry->canBeDeletedSafely()) {
            throw ValidationException::withMessages([
                'cotton_entry' => 'This cotton entry cannot be deleted because the linked stock has already been consumed or depends on downstream operations.',
            ]);
        }

        DB::transaction(function () use ($entry, $transaction): void {
            $transaction->delete();
            $entry->delete();
        });
    }

    public function resolveLinkedTransaction(CottonEntry $entry): InventoryTransaction
    {
        $transaction = $entry->inventoryTransaction;

        if (! $transaction) {
            throw ValidationException::withMessages([
                'cotton_entry' => 'The linked inventory intake transaction could not be found for this cotton entry.',
            ]);
        }

        return $transaction;
    }

    public function guardSafeUpdate(
        CottonEntry $entry,
        InventoryTransaction $transaction,
        float $newNetWeight,
        int $newInventoryItemId
    ): void {
        if ($newNetWeight < 0) {
            throw ValidationException::withMessages([
                'gross_weight' => 'Net weight cannot be negative.',
            ]);
        }

        if ($newInventoryItemId !== $entry->inventory_item_id) {
            throw ValidationException::withMessages([
                'inventory_item_id' => 'Changing the material category is blocked because it would move already-posted intake stock to a different inventory item.',
            ]);
        }

        if (! $entry->canReduceIntakeTo($newNetWeight)) {
            throw ValidationException::withMessages([
                'gross_weight' => 'This update would reduce intake below the stock already consumed from this material category.',
            ]);
        }

        if (
            $transaction->transaction_type !== InventoryTransaction::TYPE_INTAKE
            || $transaction->reference_type !== InventoryTransaction::REFERENCE_COTTON_ENTRY
            || (int) $transaction->reference_id !== $entry->id
        ) {
            throw ValidationException::withMessages([
                'cotton_entry' => 'The linked inventory transaction is not in a safe state for automatic synchronization.',
            ]);
        }
    }
}
