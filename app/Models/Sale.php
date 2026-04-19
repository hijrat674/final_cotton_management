<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'sale_date',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'sold_by',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'total_amount' => 'decimal:2',
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
            self::STATUS_PARTIAL => 'Partially Paid',
            self::STATUS_PAID => 'Paid',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class)->orderByDesc('payment_date')->orderByDesc('id');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'reference_id')
            ->where('reference_type', InventoryTransaction::REFERENCE_SALE);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')
            ->where('reference_type', JournalEntry::REFERENCE_SALE);
    }

    public function scopeFilterCustomer(Builder $query, ?string $customerId): Builder
    {
        return $query->when($customerId, fn (Builder $builder, string $value) => $builder
            ->where('customer_id', $value));
    }

    public function scopeFilterDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $builder, string $value) => $builder->whereDate('sale_date', '>=', $value))
            ->when($to, fn (Builder $builder, string $value) => $builder->whereDate('sale_date', '<=', $value));
    }

    public function scopeSortBySaleDate(Builder $query, string $sort = 'latest'): Builder
    {
        return $sort === 'oldest'
            ? $query->orderBy('sale_date')->orderBy('id')
            : $query->orderByDesc('sale_date')->orderByDesc('id');
    }

    public function getPaymentStatusAttribute(): string
    {
        if ((float) $this->remaining_amount <= 0) {
            return self::STATUS_PAID;
        }

        if ((float) $this->paid_amount > 0) {
            return self::STATUS_PARTIAL;
        }

        return self::STATUS_UNPAID;
    }

    public function canBeDeletedSafely(): bool
    {
        return (float) $this->paid_amount === 0.0 && ! $this->payments()->exists();
    }
}
