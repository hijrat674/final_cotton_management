<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionStageOutput extends Model
{
    use HasFactory;

    public const TYPE_MAIN_OUTPUT = 'main_output';
    public const TYPE_BYPRODUCT = 'byproduct';
    public const TYPE_WASTE = 'waste';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'production_stage_id',
        'inventory_item_id',
        'output_type',
        'quantity',
        'unit',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function outputTypeOptions(): array
    {
        return [
            self::TYPE_MAIN_OUTPUT => 'Main Output',
            self::TYPE_BYPRODUCT => 'Byproduct',
            self::TYPE_WASTE => 'Waste',
        ];
    }

    public function productionStage(): BelongsTo
    {
        return $this->belongsTo(ProductionStage::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
