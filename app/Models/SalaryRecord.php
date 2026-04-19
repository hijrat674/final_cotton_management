<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryRecord extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'salary_month',
        'salary_year',
        'basic_salary',
        'bonus',
        'deduction',
        'advance_deduction',
        'total_salary',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deduction' => 'decimal:2',
            'advance_deduction' => 'decimal:2',
            'total_salary' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function paymentStatusOptions(): array
    {
        return [
            self::STATUS_UNPAID => 'Unpaid',
            self::STATUS_PARTIAL => 'Partial',
            self::STATUS_PAID => 'Paid',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function monthOptions(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class)->orderByDesc('payment_date')->orderByDesc('id');
    }

    public function scopeFilterEmployee(Builder $query, ?string $employeeId): Builder
    {
        return $query->when($employeeId, fn (Builder $builder, string $value) => $builder->where('employee_id', $value));
    }

    public function scopeFilterMonth(Builder $query, ?string $month): Builder
    {
        return $query->when($month, fn (Builder $builder, string $value) => $builder->where('salary_month', $value));
    }

    public function scopeFilterYear(Builder $query, ?string $year): Builder
    {
        return $query->when($year, fn (Builder $builder, string $value) => $builder->where('salary_year', $value));
    }

    public function scopeSortByPeriod(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->orderBy('salary_year')->orderBy('salary_month')->orderBy('id')
            : $query->orderByDesc('salary_year')->orderByDesc('salary_month')->orderByDesc('id');
    }

    public function getPeriodLabelAttribute(): string
    {
        return (self::monthOptions()[$this->salary_month] ?? 'Unknown').' '.$this->salary_year;
    }

    public function getGrossSalaryAttribute(): float
    {
        return round((float) $this->basic_salary + (float) $this->bonus - (float) $this->deduction, 2);
    }
}
