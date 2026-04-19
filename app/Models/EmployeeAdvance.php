<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdvance extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_DEDUCTED = 'deducted';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'amount',
        'advance_date',
        'reason',
        'status',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'advance_date' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_DEDUCTED => 'Deducted',
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

    public function scopeFilterEmployee(Builder $query, ?string $employeeId): Builder
    {
        return $query->when($employeeId, fn (Builder $builder, string $value) => $builder->where('employee_id', $value));
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $builder, string $value) => $builder->where('status', $value));
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
