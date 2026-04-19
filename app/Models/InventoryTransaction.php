<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InventoryTransaction extends Model
{
    use HasFactory;

    public const TYPE_INTAKE = 'intake';
    public const TYPE_PRODUCTION_INPUT = 'production_input';
    public const TYPE_PRODUCTION_OUTPUT = 'production_output';
    public const TYPE_SALE = 'sale';
    public const TYPE_PAYMENT_ADJUSTMENT = 'payment_adjustment';
    public const TYPE_MANUAL_ADJUSTMENT = 'manual_adjustment';

    public const REFERENCE_COTTON_ENTRY = 'cotton_entry';
    public const REFERENCE_PRODUCTION_STAGE = 'production_stage';
    public const REFERENCE_SALE = 'sale';
    public const REFERENCE_MANUAL_ADJUSTMENT = 'manual_adjustment';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'inventory_item_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'quantity_in',
        'quantity_out',
        'transaction_date',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_in' => 'decimal:3',
            'quantity_out' => 'decimal:3',
            'transaction_date' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function transactionTypeOptions(): array
    {
        return [
            self::TYPE_INTAKE => 'Intake',
            self::TYPE_PRODUCTION_INPUT => 'Production Input',
            self::TYPE_PRODUCTION_OUTPUT => 'Production Output',
            self::TYPE_SALE => 'Sale',
            self::TYPE_PAYMENT_ADJUSTMENT => 'Payment Adjustment',
            self::TYPE_MANUAL_ADJUSTMENT => 'Manual Adjustment',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function referenceTypeOptions(): array
    {
        return [
            self::REFERENCE_COTTON_ENTRY => 'Cotton Entry',
            self::REFERENCE_PRODUCTION_STAGE => 'Production Stage',
            self::REFERENCE_SALE => 'Sale',
            self::REFERENCE_MANUAL_ADJUSTMENT => 'Manual Adjustment',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cottonEntry(): HasOne
    {
        return $this->hasOne(CottonEntry::class, 'id', 'reference_id')
            ->where('reference_type', self::REFERENCE_COTTON_ENTRY);
    }

    public function productionStage(): HasOne
    {
        return $this->hasOne(ProductionStage::class, 'id', 'reference_id')
            ->where('reference_type', self::REFERENCE_PRODUCTION_STAGE);
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class, 'id', 'reference_id')
            ->where('reference_type', self::REFERENCE_SALE);
    }

    public function scopeFilterType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $builder, string $value) => $builder
            ->where('transaction_type', $value));
    }

    public function scopeFilterItem(Builder $query, ?string $itemId): Builder
    {
        return $query->when($itemId, fn (Builder $builder, string $value) => $builder
            ->where('inventory_item_id', $value));
    }

    public function scopeFilterDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $builder, string $value) => $builder->whereDate('transaction_date', '>=', $value))
            ->when($to, fn (Builder $builder, string $value) => $builder->whereDate('transaction_date', '<=', $value));
    }

    public function scopeSortByTransactionDate(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->orderBy('transaction_date')->orderBy('id')
            : $query->orderByDesc('transaction_date')->orderByDesc('id');
    }

    public function getNetQuantityAttribute(): float
    {
        return round((float) $this->quantity_in - (float) $this->quantity_out, 3);
    }

    public function isStockIn(): bool
    {
        return (float) $this->quantity_in > 0;
    }
}
