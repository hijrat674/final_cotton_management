<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPayment extends Model
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
        'salary_record_id',
        'employee_id',
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

    public function salaryRecord(): BelongsTo
    {
        return $this->belongsTo(SalaryRecord::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

}
