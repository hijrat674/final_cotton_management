<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'position',
        'department',
        'salary',
        'hire_date',
        'address',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'hire_date' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productionStages(): HasMany
    {
        return $this->hasMany(ProductionStage::class, 'handled_by_employee_id');
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeFilterName(Builder $query, ?string $name): Builder
    {
        return $query->when($name, fn (Builder $builder, string $value) => $builder
            ->where('full_name', 'like', '%'.$value.'%'));
    }

    public function scopeFilterPhone(Builder $query, ?string $phone): Builder
    {
        return $query->when($phone, fn (Builder $builder, string $value) => $builder
            ->where('phone', 'like', '%'.$value.'%'));
    }

    public function scopeFilterDepartment(Builder $query, ?string $department): Builder
    {
        return $query->when($department, fn (Builder $builder, string $value) => $builder
            ->where('department', $value));
    }

    public function scopeFilterPosition(Builder $query, ?string $position): Builder
    {
        return $query->when($position, fn (Builder $builder, string $value) => $builder
            ->where('position', $value));
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $builder, string $value) => $builder
            ->where('status', $value));
    }

    public function scopeSortByCreated(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest' ? $query->oldest() : $query->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function canBeDeletedSafely(): bool
    {
        return ! $this->productionStages()->exists()
            && ! $this->salaryRecords()->exists()
            && ! $this->salaryPayments()->exists()
            && ! $this->advances()->exists();
    }
}
