<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CottonEntry extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'truck_number',
        'driver_name',
        'driver_phone',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'entry_date',
        'inventory_item_id',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gross_weight' => 'decimal:3',
            'tare_weight' => 'decimal:3',
            'net_weight' => 'decimal:3',
            'entry_date' => 'date',
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

    public function inventoryTransaction(): HasOne
    {
        return $this->hasOne(InventoryTransaction::class, 'reference_id')
            ->where('reference_type', InventoryTransaction::REFERENCE_COTTON_ENTRY);
    }

    public function scopeFilterTruckNumber(Builder $query, ?string $truckNumber): Builder
    {
        return $query->when($truckNumber, fn (Builder $builder, string $value) => $builder
            ->where('truck_number', 'like', '%'.$value.'%'));
    }

    public function scopeFilterDriverName(Builder $query, ?string $driverName): Builder
    {
        return $query->when($driverName, fn (Builder $builder, string $value) => $builder
            ->where('driver_name', 'like', '%'.$value.'%'));
    }

    public function scopeFilterEntryDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $builder, string $value) => $builder->whereDate('entry_date', '>=', $value))
            ->when($to, fn (Builder $builder, string $value) => $builder->whereDate('entry_date', '<=', $value));
    }

    public function scopeSortByEntryDate(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->orderBy('entry_date')->orderBy('id')
            : $query->orderByDesc('entry_date')->orderByDesc('id');
    }

    public function canReduceIntakeTo(float $newNetWeight): bool
    {
        $difference = round((float) $this->net_weight - $newNetWeight, 3);

        if ($difference <= 0) {
            return true;
        }

        $this->inventoryItem->loadSum('transactions as total_quantity_in', 'quantity_in');
        $this->inventoryItem->loadSum('transactions as total_quantity_out', 'quantity_out');

        return $this->inventoryItem->current_stock >= $difference;
    }

    public function canBeDeletedSafely(): bool
    {
        $this->inventoryItem->loadSum('transactions as total_quantity_in', 'quantity_in');
        $this->inventoryItem->loadSum('transactions as total_quantity_out', 'quantity_out');

        return $this->inventoryItem->current_stock >= (float) $this->net_weight;
    }
}
