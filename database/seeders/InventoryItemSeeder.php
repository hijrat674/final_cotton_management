<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'product_name' => 'Raw Cotton',
                'product_code' => 'RM-RAW-COTTON',
                'product_type' => InventoryItem::TYPE_RAW_MATERIAL,
                'unit' => InventoryItem::UNIT_TON,
                'minimum_stock' => 20,
                'notes' => 'Primary intake material for factory processing.',
            ],
            [
                'product_name' => 'Processed Cotton',
                'product_code' => 'SF-PROCESSED-COTTON',
                'product_type' => InventoryItem::TYPE_SEMI_FINISHED,
                'unit' => InventoryItem::UNIT_TON,
                'minimum_stock' => 10,
                'notes' => 'Intermediate output after cleaning and processing.',
            ],
            [
                'product_name' => 'Kernel',
                'product_code' => 'BP-KERNEL',
                'product_type' => InventoryItem::TYPE_BYPRODUCT,
                'unit' => InventoryItem::UNIT_KG,
                'minimum_stock' => 500,
                'notes' => 'Kernel byproduct retained for downstream use.',
            ],
            [
                'product_name' => 'Cotton Oil',
                'product_code' => 'FP-COTTON-OIL',
                'product_type' => InventoryItem::TYPE_FINISHED_PRODUCT,
                'unit' => InventoryItem::UNIT_LITER,
                'minimum_stock' => 1000,
                'notes' => 'Finished oil inventory prepared for sale or dispatch.',
            ],
            [
                'product_name' => 'Cotton Meal',
                'product_code' => 'FP-COTTON-MEAL',
                'product_type' => InventoryItem::TYPE_FINISHED_PRODUCT,
                'unit' => InventoryItem::UNIT_KG,
                'minimum_stock' => 1200,
                'notes' => 'Finished meal product for commercial distribution.',
            ],
            [
                'product_name' => 'Waste',
                'product_code' => 'WS-WASTE',
                'product_type' => InventoryItem::TYPE_WASTE,
                'unit' => InventoryItem::UNIT_KG,
                'minimum_stock' => 0,
                'notes' => 'Tracked waste material for reporting purposes.',
            ],
            [
                'product_name' => 'Shell',
                'product_code' => 'BP-SHELL',
                'product_type' => InventoryItem::TYPE_BYPRODUCT,
                'unit' => InventoryItem::UNIT_KG,
                'minimum_stock' => 300,
                'notes' => 'Shell byproduct stock available for disposal or reuse.',
            ],
        ];

        foreach ($items as $attributes) {
            InventoryItem::query()->updateOrCreate(
                ['product_code' => $attributes['product_code']],
                $attributes,
            );
        }
    }
}
