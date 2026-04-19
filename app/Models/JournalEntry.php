<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    public const REFERENCE_SALE = 'sale';
    public const REFERENCE_PAYMENT = 'payment';
    public const REFERENCE_EXPENSE = 'expense';
    public const REFERENCE_PRODUCTION = 'production';
    public const REFERENCE_SALARY_PAYMENT = 'salary_payment';
    public const REFERENCE_EMPLOYEE_ADVANCE = 'employee_advance';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function referenceTypeOptions(): array
    {
        return [
            self::REFERENCE_SALE => 'Sale',
            self::REFERENCE_PAYMENT => 'Payment',
            self::REFERENCE_EXPENSE => 'Expense',
            self::REFERENCE_PRODUCTION => 'Production',
            self::REFERENCE_SALARY_PAYMENT => 'Salary Payment',
            self::REFERENCE_EMPLOYEE_ADVANCE => 'Employee Advance',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function scopeFilterReferenceType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $builder, string $value) => $builder->where('reference_type', $value));
    }

    public function scopeFilterDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $builder, string $value) => $builder->whereDate('entry_date', '>=', $value))
            ->when($to, fn (Builder $builder, string $value) => $builder->whereDate('entry_date', '<=', $value));
    }
}
