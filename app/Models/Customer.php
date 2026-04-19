<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'phone',
        'address',
        'notes',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
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

    public function scopeSortByCreated(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->oldest()
            : $query->latest();
    }

    public function getOutstandingBalanceAttribute(): float
    {
        $amount = array_key_exists('sales_sum_remaining_amount', $this->attributes)
            ? (float) $this->attributes['sales_sum_remaining_amount']
            : (float) $this->sales()->sum('remaining_amount');

        return round($amount, 2);
    }

    public function hasSalesHistory(): bool
    {
        return $this->sales()->exists();
    }
}
