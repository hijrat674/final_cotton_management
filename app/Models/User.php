<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_PRODUCTION = 'production';
    public const ROLE_SALES = 'sales';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_PRODUCTION => 'Production',
            self::ROLE_SALES => 'Sales',
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

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'created_by');
    }

    public function cottonEntries(): HasMany
    {
        return $this->hasMany(CottonEntry::class, 'created_by');
    }

    public function handledProductionStages(): HasMany
    {
        return $this->hasMany(ProductionStage::class, 'handled_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'sold_by');
    }

    public function receivedSalePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'received_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'created_by');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function createdSalaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class, 'created_by');
    }

    public function receivedSalaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class, 'received_by');
    }

    public function createdEmployeeAdvances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class, 'created_by');
    }

    public function scopeFilterName(Builder $query, ?string $name): Builder
    {
        return $query->when($name, fn (Builder $builder, string $value) => $builder
            ->where('name', 'like', '%'.$value.'%'));
    }

    public function scopeFilterEmail(Builder $query, ?string $email): Builder
    {
        return $query->when($email, fn (Builder $builder, string $value) => $builder
            ->where('email', 'like', '%'.$value.'%'));
    }

    public function scopeFilterRole(Builder $query, ?string $role): Builder
    {
        return $query->when($role, fn (Builder $builder, string $value) => $builder
            ->where('role', $value));
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $builder, string $value) => $builder
            ->where('status', $value));
    }

    public function scopeSortByCreated(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->oldest()
            : $query->latest();
    }
}
