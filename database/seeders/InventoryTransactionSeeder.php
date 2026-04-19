<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUserId = User::query()->where('role', User::ROLE_ADMIN)->value('id')
            ?? User::query()->value('id');

        if (! $adminUserId) {
            return;
        }

        $items = InventoryItem::query()->get()->keyBy('product_code');

        $transactions = [
            [
                'inventory_item_id' => $items['RM-RAW-COTTON']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_INTAKE,
                'reference_type' => InventoryTransaction::REFERENCE_COTTON_ENTRY,
                'reference_id' => 1001,
                'quantity_in' => 85,
                'quantity_out' => 0,
                'transaction_date' => now()->subDays(12)->toDateString(),
                'notes' => 'Initial cotton intake for current operating cycle.',
            ],
            [
                'inventory_item_id' => $items['RM-RAW-COTTON']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_PRODUCTION_INPUT,
                'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
                'reference_id' => 2101,
                'quantity_in' => 0,
                'quantity_out' => 42,
                'transaction_date' => now()->subDays(8)->toDateString(),
                'notes' => 'Issued raw cotton to production.',
            ],
            [
                'inventory_item_id' => $items['SF-PROCESSED-COTTON']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_PRODUCTION_OUTPUT,
                'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
                'reference_id' => 2101,
                'quantity_in' => 31.5,
                'quantity_out' => 0,
                'transaction_date' => now()->subDays(7)->toDateString(),
                'notes' => 'Processed cotton received from production.',
            ],
            [
                'inventory_item_id' => $items['BP-KERNEL']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_PRODUCTION_OUTPUT,
                'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
                'reference_id' => 2101,
                'quantity_in' => 2400,
                'quantity_out' => 0,
                'transaction_date' => now()->subDays(6)->toDateString(),
                'notes' => 'Kernel byproduct recorded.',
            ],
            [
                'inventory_item_id' => $items['FP-COTTON-OIL']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_INTAKE,
                'reference_type' => InventoryTransaction::REFERENCE_MANUAL_ADJUSTMENT,
                'reference_id' => 501,
                'quantity_in' => 1800,
                'quantity_out' => 0,
                'transaction_date' => now()->subDays(5)->toDateString(),
                'notes' => 'Opening stock adjusted after tank verification.',
            ],
            [
                'inventory_item_id' => $items['FP-COTTON-MEAL']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_SALE,
                'reference_type' => InventoryTransaction::REFERENCE_SALE,
                'reference_id' => 9001,
                'quantity_in' => 0,
                'quantity_out' => 150,
                'transaction_date' => now()->subDays(3)->toDateString(),
                'notes' => 'Customer dispatch against sales order.',
            ],
            [
                'inventory_item_id' => $items['FP-COTTON-MEAL']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_INTAKE,
                'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
                'reference_id' => 2103,
                'quantity_in' => 980,
                'quantity_out' => 0,
                'transaction_date' => now()->subDays(4)->toDateString(),
                'notes' => 'Meal stock received from production.',
            ],
            [
                'inventory_item_id' => $items['WS-WASTE']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_MANUAL_ADJUSTMENT,
                'reference_type' => InventoryTransaction::REFERENCE_MANUAL_ADJUSTMENT,
                'reference_id' => 777,
                'quantity_in' => 140,
                'quantity_out' => 0,
                'transaction_date' => now()->subDays(2)->toDateString(),
                'notes' => 'Waste tracking entry.',
            ],
            [
                'inventory_item_id' => $items['BP-SHELL']->id ?? null,
                'transaction_type' => InventoryTransaction::TYPE_PRODUCTION_OUTPUT,
                'reference_type' => InventoryTransaction::REFERENCE_PRODUCTION_STAGE,
                'reference_id' => 2104,
                'quantity_in' => 260,
                'quantity_out' => 0,
                'transaction_date' => now()->subDay()->toDateString(),
                'notes' => 'Shell stock from recent process batch.',
            ],
        ];

        foreach ($transactions as $attributes) {
            if (! $attributes['inventory_item_id']) {
                continue;
            }

            InventoryTransaction::query()->updateOrCreate(
                [
                    'inventory_item_id' => $attributes['inventory_item_id'],
                    'transaction_type' => $attributes['transaction_type'],
                    'reference_type' => $attributes['reference_type'],
                    'reference_id' => $attributes['reference_id'],
                ],
                $attributes + ['created_by' => $adminUserId],
            );
        }
    }
}
