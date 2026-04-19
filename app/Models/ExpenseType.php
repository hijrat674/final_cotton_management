<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends Model
{
    use HasFactory;

    public const NAME_PRODUCTION = 'production';
    public const NAME_TRANSPORT = 'transport';
    public const NAME_SALARY = 'salary';
    public const NAME_MAINTENANCE = 'maintenance';
    public const NAME_UTILITY = 'utility';
    public const NAME_OTHER = 'other';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultOptions(): array
    {
        return [
            self::NAME_PRODUCTION => 'Production',
            self::NAME_TRANSPORT => 'Transport',
            self::NAME_SALARY => 'Salary',
            self::NAME_MAINTENANCE => 'Maintenance',
            self::NAME_UTILITY => 'Utility',
            self::NAME_OTHER => 'Other',
        ];
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
