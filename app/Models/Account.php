<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    public const TYPE_ASSET = 'asset';
    public const TYPE_LIABILITY = 'liability';
    public const TYPE_EQUITY = 'equity';
    public const TYPE_REVENUE = 'revenue';
    public const TYPE_EXPENSE = 'expense';

    public const CODE_CASH = '1000';
    public const CODE_ACCOUNTS_RECEIVABLE = '1100';
    public const CODE_EMPLOYEE_ADVANCE = '1200';
    public const CODE_SALES_REVENUE = '4000';
    public const CODE_PRODUCTION_EXPENSE = '5000';
    public const CODE_GENERAL_EXPENSE = '5100';
    public const CODE_SALARY_EXPENSE = '5200';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'account_name',
        'account_type',
        'parent_id',
        'code',
    ];

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_ASSET => 'Asset',
            self::TYPE_LIABILITY => 'Liability',
            self::TYPE_EQUITY => 'Equity',
            self::TYPE_REVENUE => 'Revenue',
            self::TYPE_EXPENSE => 'Expense',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function scopeFilterType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $builder, string $value) => $builder->where('account_type', $value));
    }

    public function scopeFilterSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn (Builder $builder, string $value) => $builder
            ->where(function (Builder $nested) use ($value): void {
                $nested
                    ->where('account_name', 'like', '%'.$value.'%')
                    ->orWhere('code', 'like', '%'.$value.'%');
            }));
    }

    public function getBalanceAttribute(): float
    {
        $totalDebit = array_key_exists('journal_entry_lines_sum_debit', $this->attributes)
            ? (float) $this->attributes['journal_entry_lines_sum_debit']
            : (float) $this->journalEntryLines()->sum('debit');

        $totalCredit = array_key_exists('journal_entry_lines_sum_credit', $this->attributes)
            ? (float) $this->attributes['journal_entry_lines_sum_credit']
            : (float) $this->journalEntryLines()->sum('credit');

        if (in_array($this->account_type, [self::TYPE_ASSET, self::TYPE_EXPENSE], true)) {
            return round($totalDebit - $totalCredit, 2);
        }

        return round($totalCredit - $totalDebit, 2);
    }
}
