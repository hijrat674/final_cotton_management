<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalePayment extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'cash';
    public const METHOD_BANK = 'bank';
    public const METHOD_TRANSFER = 'transfer';
    public const METHOD_OTHER = 'other';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'payment_date',
        'amount',
        'payment_method',
        'received_by',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function paymentMethodOptions(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_BANK => 'Bank',
            self::METHOD_TRANSFER => 'Transfer',
            self::METHOD_OTHER => 'Other',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')
            ->where('reference_type', JournalEntry::REFERENCE_PAYMENT);
    }
}
