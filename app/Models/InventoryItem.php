<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    public const TYPE_RAW_MATERIAL = 'raw_material';
    public const TYPE_SEMI_FINISHED = 'semi_finished';
    public const TYPE_FINISHED_PRODUCT = 'finished_product';
    public const TYPE_BYPRODUCT = 'byproduct';
    public const TYPE_WASTE = 'waste';

    public const UNIT_TON = 'ton';
    public const UNIT_KG = 'kg';
    public const UNIT_LITER = 'liter';

    public const STATUS_NORMAL = 'normal';
    public const STATUS_LOW_STOCK = 'low_stock';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'product_name',
        'product_code',
        'product_type',
        'unit',
        'minimum_stock',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:3',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function productTypeOptions(): array
    {
        return [
            self::TYPE_RAW_MATERIAL => 'Raw Material',
            self::TYPE_SEMI_FINISHED => 'Semi Finished',
            self::TYPE_FINISHED_PRODUCT => 'Finished Product',
            self::TYPE_BYPRODUCT => 'Byproduct',
            self::TYPE_WASTE => 'Waste',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function unitOptions(): array
    {
        return [
            self::UNIT_TON => 'Ton',
            self::UNIT_KG => 'Kg',
            self::UNIT_LITER => 'Liter',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function stockStatusOptions(): array
    {
        return [
            self::STATUS_NORMAL => 'Normal',
            self::STATUS_LOW_STOCK => 'Low Stock',
            self::STATUS_OUT_OF_STOCK => 'Out of Stock',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cottonEntries(): HasMany
    {
        return $this->hasMany(CottonEntry::class);
    }

    public function sourceProductionStages(): HasMany
    {
        return $this->hasMany(ProductionStage::class, 'source_inventory_item_id');
    }

    public function productionStageOutputs(): HasMany
    {
        return $this->hasMany(ProductionStageOutput::class);
    }

    public function scopeFilterName(Builder $query, ?string $name): Builder
    {
        return $query->when($name, fn (Builder $builder, string $value) => $builder
            ->where('product_name', 'like', '%'.$value.'%'));
    }

    public function scopeFilterType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $builder, string $value) => $builder
            ->where('product_type', $value));
    }

    public function scopeFilterUnit(Builder $query, ?string $unit): Builder
    {
        return $query->when($unit, fn (Builder $builder, string $value) => $builder
            ->where('unit', $value));
    }

    public function scopeSortByCreated(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->oldest()
            : $query->latest();
    }

    public function scopeWithStockSummary(Builder $query): Builder
    {
        return $query
            ->withSum('transactions as total_quantity_in', 'quantity_in')
            ->withSum('transactions as total_quantity_out', 'quantity_out');
    }

    public function getCurrentStockAttribute(): float
    {
        $quantityIn = array_key_exists('total_quantity_in', $this->attributes)
            ? (float) $this->attributes['total_quantity_in']
            : (float) $this->transactions()->sum('quantity_in');

        $quantityOut = array_key_exists('total_quantity_out', $this->attributes)
            ? (float) $this->attributes['total_quantity_out']
            : (float) $this->transactions()->sum('quantity_out');

        return round($quantityIn - $quantityOut, 3);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return self::STATUS_OUT_OF_STOCK;
        }

        if ($this->current_stock < (float) $this->minimum_stock) {
            return self::STATUS_LOW_STOCK;
        }

        return self::STATUS_NORMAL;
    }

    public function hasTransactions(): bool
    {
        return $this->transactions()->exists();
    }

    public static function lowStockCount(?Collection $items = null): int
    {
        $items ??= self::query()->withStockSummary()->get();

        return $items->filter(fn (self $item) => $item->stock_status === self::STATUS_LOW_STOCK)->count();
    }

    public static function outOfStockCount(?Collection $items = null): int
    {
        $items ??= self::query()->withStockSummary()->get();

        return $items->filter(fn (self $item) => $item->stock_status === self::STATUS_OUT_OF_STOCK)->count();
    }
}
