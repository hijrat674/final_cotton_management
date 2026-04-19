<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'account_name' => 'Cash',
                'account_type' => Account::TYPE_ASSET,
                'parent_id' => null,
                'code' => Account::CODE_CASH,
            ],
            [
                'account_name' => 'Accounts Receivable',
                'account_type' => Account::TYPE_ASSET,
                'parent_id' => null,
                'code' => Account::CODE_ACCOUNTS_RECEIVABLE,
            ],
            [
                'account_name' => 'Employee Advance',
                'account_type' => Account::TYPE_ASSET,
                'parent_id' => null,
                'code' => Account::CODE_EMPLOYEE_ADVANCE,
            ],
            [
                'account_name' => 'Sales Revenue',
                'account_type' => Account::TYPE_REVENUE,
                'parent_id' => null,
                'code' => Account::CODE_SALES_REVENUE,
            ],
            [
                'account_name' => 'Production Expense',
                'account_type' => Account::TYPE_EXPENSE,
                'parent_id' => null,
                'code' => Account::CODE_PRODUCTION_EXPENSE,
            ],
            [
                'account_name' => 'General Expense',
                'account_type' => Account::TYPE_EXPENSE,
                'parent_id' => null,
                'code' => Account::CODE_GENERAL_EXPENSE,
            ],
            [
                'account_name' => 'Salary Expense',
                'account_type' => Account::TYPE_EXPENSE,
                'parent_id' => null,
                'code' => Account::CODE_SALARY_EXPENSE,
            ],
        ];

        foreach ($accounts as $account) {
            Account::query()->updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
}
