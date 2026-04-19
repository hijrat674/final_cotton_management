<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'expense_type_id',
        'amount',
        'expense_date',
        'production_stage_id',
        'employee_id',
        'created_by',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function productionStage(): BelongsTo
    {
        return $this->belongsTo(ProductionStage::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')
            ->whereIn('reference_type', [JournalEntry::REFERENCE_EXPENSE, JournalEntry::REFERENCE_PRODUCTION]);
    }

    public function scopeFilterTitle(Builder $query, ?string $title): Builder
    {
        return $query->when($title, fn (Builder $builder, string $value) => $builder
            ->where('title', 'like', '%'.$value.'%'));
    }

    public function scopeFilterExpenseType(Builder $query, ?string $expenseTypeId): Builder
    {
        return $query->when($expenseTypeId, fn (Builder $builder, string $value) => $builder
            ->where('expense_type_id', $value));
    }

    public function scopeFilterProductionStage(Builder $query, ?string $productionStageId): Builder
    {
        return $query->when($productionStageId, function (Builder $builder, string $value): Builder {
            if ($value === 'general') {
                return $builder->whereNull('production_stage_id');
            }

            return $builder->where('production_stage_id', $value);
        });
    }

    public function scopeFilterDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $builder, string $value) => $builder->whereDate('expense_date', '>=', $value))
            ->when($to, fn (Builder $builder, string $value) => $builder->whereDate('expense_date', '<=', $value));
    }

    public function scopeSortByExpenseDate(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->orderBy('expense_date')->orderBy('id')
            : $query->orderByDesc('expense_date')->orderByDesc('id');
    }

    public function isProductionLinked(): bool
    {
        return $this->production_stage_id !== null;
    }
}
