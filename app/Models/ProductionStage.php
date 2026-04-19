<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionStage extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'stage_name',
        'source_inventory_item_id',
        'input_quantity',
        'stage_date',
        'handled_by',
        'handled_by_employee_id',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_quantity' => 'decimal:3',
            'stage_date' => 'date',
        ];
    }

    public function sourceInventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'source_inventory_item_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function handledByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'handled_by_employee_id');
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(ProductionStageOutput::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function productionExpenses(): HasMany
    {
        return $this->expenses()->whereHas('expenseType', function ($query) {
            $query->where('name', ExpenseType::NAME_PRODUCTION);
        });
    }

    public function scopeFilterStageName(Builder $query, ?string $stageName): Builder
    {
        return $query->when($stageName, fn (Builder $builder, string $value) => $builder
            ->where('stage_name', 'like', '%'.$value.'%'));
    }

    public function scopeFilterStageDate(Builder $query, ?string $date): Builder
    {
        return $query->when($date, fn (Builder $builder, string $value) => $builder
            ->whereDate('stage_date', $value));
    }

    public function scopeSortByStageDate(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->orderBy('stage_date')->orderBy('id')
            : $query->orderByDesc('stage_date')->orderByDesc('id');
    }

    public function getTotalOutputQuantityAttribute(): float
    {
        $total = array_key_exists('outputs_sum_quantity', $this->attributes)
            ? (float) $this->attributes['outputs_sum_quantity']
            : (float) $this->outputs()->sum('quantity');

        return round($total, 3);
    }
}
