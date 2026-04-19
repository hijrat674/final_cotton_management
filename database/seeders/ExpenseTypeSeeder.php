<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (array_keys(ExpenseType::defaultOptions()) as $name) {
            ExpenseType::query()->updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }
}
