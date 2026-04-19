<?php

namespace App\Services\Production;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\ProductionStage;
use App\Models\ProductionStageOutput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionStageService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createStage(array $attributes): ProductionStage
    {
        return DB::transaction(function () use ($attributes): ProductionStage {
            $sourceItem = $this->lockInventoryItem((int) $attributes['source_inventory_item_id']);
            $outputItems = $this->lockOutputItems($attributes['outputs']);

            $this->validateStagePayload($attributes, $sourceItem, $outputItems);

            $stage = ProductionStage::create([
                'stage_name' => $attributes['stage_name'],
                'source_inventory_item_id' => $sourceItem->id,
                'input_quantity' => $attributes['input_quantity'],
                'stage_date' => $attributes['stage_date'],
                'handled_by' => $attributes['handled_by'],
                'handled_by_employee_id' => $attributes['handled_by_employee_id'],
                'notes' => $attributes['notes'] ?? null,
            ]);

            $this->syncOutputs($stage, $attributes['outputs']);
            $this->syncInventoryTransactions($stage);
            $this->syncExpense($stage, (float) $attributes['total_cost'], (int) $attributes['created_by']);

            return $stage->load([
                'sourceInventoryItem',
                'handler',
                'handledByEmployee',
                'outputs.inventoryItem',
                'expenses.expenseType',
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateStage(ProductionStage $stage, array $attributes): ProductionStage
    {
        return DB::transaction(function () use ($stage, $attributes): ProductionStage {
            $stage->loadMissing(['outputs', 'expenses.expenseType']);
            $this->lockInventoryItemsForDelete($stage);

            if ($this->hasDownstreamDependencies($stage)) {
                throw ValidationException::withMessages([
                    'production_stage' => 'This production stage cannot be edited because one or more of its outputs are already used by later production or stock-out activity.',
                ]);
            }

            $this->lockInventoryItemsForUpdate($stage, $attributes);

            $sourceItem = InventoryItem::query()->lockForUpdate()->findOrFail((int) $attributes['source_inventory_item_id']);
            $outputItems = $this->lockOutputItems($attributes['outputs']);

            $availableStock = $this->availableSourceStockForUpdate($stage, $sourceItem);
            $this->validateStagePayload($attributes, $sourceItem, $outputItems, $availableStock);

            $stage->update([
                'stage_name' => $attributes['stage_name'],
                'source_inventory_item_id' => $sourceItem->id,
                'input_quantity' => $attributes['input_quantity'],
                'stage_date' => $attributes['stage_date'],
                'handled_by' => $attributes['handled_by'],
                'handled_by_employee_id' => $attributes['handled_by_employee_id'],
                'notes' => $attributes['notes'] ?? null,
            ]);

            $stage->outputs()->delete();
            $this->deleteInventoryTransactions($stage);

            $this->syncOutputs($stage, $attributes['outputs']);
            $stage->unsetRelation('outputs');
            $this->syncInventoryTransactions($stage);
            $this->syncExpense($stage, (float) $attributes['total_cost'], (int) $attributes['created_by']);

            return $stage->load([
                'sourceInventoryItem',
                'handler',
                'handledByEmployee',
                'outputs.inventoryItem',
                'expenses.expenseType',
            ]);
        });
    }

    public function deleteStage(ProductionStage $stage): void
    {
        DB::transaction(function () use ($stage): void {
            $stage->loadMissing(['outputs', 'expenses.expenseType']);
            $this->lockInventoryItemsForDelete($stage);

            if ($this->hasDownstreamDependencies($stage)) {
                throw ValidationException::withMessages([
                    'production_stage' => 'This production stage cannot be deleted because one or more of its outputs are already used by later production or stock-out activity.',
                ]);
            }

            $this->deleteInventoryTransactions($stage);
            $stage->expenses()
                ->whereHas('expenseType', fn ($query) => $query->where('name', ExpenseType::NAME_PRODUCTION))
                ->delete();
            $stage->delete();
        });
    }

    public function hasDownstreamDependencies(ProductionStage $stage): bool
    {
        $stage->loadMissing('outputs');

        foreach ($stage->outputs as $output) {
            $hasDependentTransaction = InventoryTransaction::query()
                ->where('inventory_item_id', $output->inventory_item_id)
                ->where(function ($query) {
                    $query
                        ->where('quantity_out', '>', 0)
                        ->orWhere('transaction_type', InventoryTransaction::TYPE_SALE);
                })
                ->where('created_at', '>', $stage->created_at)
                ->exists();

            if ($hasDependentTransaction) {
                return true;
            }

            $hasDependentStage = ProductionStage::query()
                ->where('source_inventory_item_id', $output->inventory_item_id)
                ->where('id', '!=', $stage->id)
                ->where('created_at', '>', $stage->created_at)
                ->exists();

            if ($hasDependentStage) {
                return true;
            }
        }

        return false;
    }

    protected function lockInventoryItem(int $inventoryItemId): InventoryItem
    {
        return InventoryItem::query()->lockForUpdate()->findOrFail($inventoryItemId);
    }

    /**
     * @param  array<int, array<string, mixed>>  $outputs
     * @return \Illuminate\Support\Collection<int, InventoryItem>
     */
    protected function lockOutputItems(array $outputs): Collection
    {
        $itemIds = collect($outputs)
            ->pluck('inventory_item_id')
            ->filter()
            ->map(fn (mixed $id) => (int) $id)
            ->unique()
            ->values();

        return InventoryItem::query()
            ->whereIn('id', $itemIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  \Illuminate\Support\Collection<int, InventoryItem>  $outputItems
     */
    protected function validateStagePayload(
        array $attributes,
        InventoryItem $sourceItem,
        Collection $outputItems,
        ?float $availableStock = null
    ): void {
        $inputQuantity = round((float) $attributes['input_quantity'], 3);

        if ($inputQuantity <= 0) {
            throw ValidationException::withMessages([
                'input_quantity' => 'Input quantity must be greater than zero.',
            ]);
        }

        $availableStock ??= $this->currentStock($sourceItem);

        if ($inputQuantity > $availableStock) {
            throw ValidationException::withMessages([
                'input_quantity' => 'Input quantity exceeds the available stock for the selected source material.',
            ]);
        }

        $totalOutputQuantity = 0.0;

        foreach ($attributes['outputs'] as $index => $output) {
            $inventoryItem = $outputItems->get((int) $output['inventory_item_id']);

            if (! $inventoryItem) {
                throw ValidationException::withMessages([
                    "outputs.$index.inventory_item_id" => 'The selected output item could not be resolved.',
                ]);
            }

            if ($inventoryItem->unit !== $output['unit']) {
                throw ValidationException::withMessages([
                    "outputs.$index.unit" => 'Output unit must match the selected inventory item unit.',
                ]);
            }

            $quantity = round((float) $output['quantity'], 3);
            $totalOutputQuantity += $quantity;
        }

        if ($totalOutputQuantity <= 0) {
            throw ValidationException::withMessages([
                'outputs' => 'At least one output with a positive quantity is required.',
            ]);
        }
    }

    protected function availableSourceStockForUpdate(ProductionStage $stage, InventoryItem $sourceItem): float
    {
        $currentStock = $this->currentStock($sourceItem);

        if ($sourceItem->id === $stage->source_inventory_item_id) {
            return round($currentStock + (float) $stage->input_quantity, 3);
        }

        return $currentStock;
    }

    protected function currentStock(InventoryItem $item): float
    {
        $item->loadSum('transactions as total_quantity_in', 'quantity_in');
        $item->loadSum('transactions as total_quantity_out', 'quantity_out');

        return $item->current_stock;
    }

    protected function syncOutputs(ProductionStage $stage, array $outputs): void
    {
        foreach ($outputs as $output) {
            $stage->outputs()->create([
                'inventory_item_id' => $output['inventory_item_id'],
                'output_type' => $output['output_type'],
                'quantity' => round((float) $output['quantity'], 3),
                'unit' => $output['unit'],
            ]);
        }
    }

    protected function syncInventoryTransactions(ProductionStage $stage): void
    {
        $stage->load('outputs');

        InventoryTransaction::create([
            'inventory_item_id' => $stage->source_inventory_item_id,
            'transaction_type' => InventoryTransaction::TYPE_PRODUCTION_INPUT,
            'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
            'reference_id' => $stage->id,
            'quantity_in' => 0,
            'quantity_out' => $stage->input_quantity,
            'transaction_date' => $stage->stage_date,
            'notes' => 'Production input issued for stage #'.$stage->id.' - '.$stage->stage_name.'.',
            'created_by' => $stage->handled_by,
        ]);

        foreach ($stage->outputs as $output) {
            InventoryTransaction::create([
                'inventory_item_id' => $output->inventory_item_id,
                'transaction_type' => InventoryTransaction::TYPE_PRODUCTION_OUTPUT,
                'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
                'reference_id' => $stage->id,
                'quantity_in' => $output->quantity,
                'quantity_out' => 0,
                'transaction_date' => $stage->stage_date,
                'notes' => ucfirst(str_replace('_', ' ', $output->output_type)).' received from production stage #'.$stage->id.'.',
                'created_by' => $stage->handled_by,
            ]);
        }
    }

    protected function syncExpense(ProductionStage $stage, float $totalCost, int $createdBy): void
    {
        $productionExpenseTypeId = ExpenseType::query()
            ->where('name', ExpenseType::NAME_PRODUCTION)
            ->value('id');

        if (! $productionExpenseTypeId) {
            throw ValidationException::withMessages([
                'total_cost' => 'The production expense type is missing. Seed the expense types before recording production costs.',
            ]);
        }

        Expense::query()->updateOrCreate(
            [
                'production_stage_id' => $stage->id,
                'expense_type_id' => $productionExpenseTypeId,
            ],
            [
                'title' => 'Production Cost - '.$stage->stage_name,
                'amount' => round($totalCost, 2),
                'expense_date' => $stage->stage_date,
                'created_by' => $createdBy,
                'notes' => 'Auto-generated from production stage #'.$stage->id.'.',
            ]
        );
    }

    protected function deleteInventoryTransactions(ProductionStage $stage): void
    {
        InventoryTransaction::query()
            ->where('reference_type', InventoryTransaction::REFERENCE_PRODUCTION_STAGE)
            ->where('reference_id', $stage->id)
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function lockInventoryItemsForUpdate(ProductionStage $stage, array $attributes): void
    {
        $itemIds = collect([$stage->source_inventory_item_id])
            ->merge($stage->outputs->pluck('inventory_item_id'))
            ->push((int) $attributes['source_inventory_item_id'])
            ->merge(collect($attributes['outputs'])->pluck('inventory_item_id')->map(fn (mixed $id) => (int) $id))
            ->unique()
            ->values();

        InventoryItem::query()
            ->whereIn('id', $itemIds)
            ->lockForUpdate()
            ->get();
    }

    protected function lockInventoryItemsForDelete(ProductionStage $stage): void
    {
        $itemIds = collect([$stage->source_inventory_item_id])
            ->merge($stage->outputs->pluck('inventory_item_id'))
            ->unique()
            ->values();

        InventoryItem::query()
            ->whereIn('id', $itemIds)
            ->lockForUpdate()
            ->get();
    }
}
