<?php

use App\Models\ExpenseType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('expenses')) {
            return;
        }

        if (Schema::hasTable('expense_types')) {
            foreach (array_keys(ExpenseType::defaultOptions()) as $name) {
                DB::table('expense_types')->updateOrInsert(
                    ['name' => $name],
                    [
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        Schema::table('expenses', function (Blueprint $table): void {
            if (! Schema::hasColumn('expenses', 'expense_type_id')) {
                $table->foreignId('expense_type_id')->nullable()->constrained('expense_types')->restrictOnDelete();
            }

            if (! Schema::hasColumn('expenses', 'expense_date')) {
                $table->date('expense_date')->nullable()->index();
            }

            if (! Schema::hasColumn('expenses', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        try {
            Schema::table('expenses', function (Blueprint $table): void {
                $table->dropUnique('expenses_production_stage_id_unique');
            });
        } catch (\Throwable) {
            // Ignore if the legacy unique index is absent or already removed.
        }

        if (Schema::hasColumn('expenses', 'expense_type')) {
            $typeMap = DB::table('expense_types')
                ->pluck('id', 'name');

            DB::table('expenses')
                ->select(['id', 'expense_type', 'created_at'])
                ->orderBy('id')
                ->chunkById(100, function ($expenses) use ($typeMap): void {
                    foreach ($expenses as $expense) {
                        DB::table('expenses')
                            ->where('id', $expense->id)
                            ->update([
                                'expense_type_id' => $typeMap[$expense->expense_type] ?? $typeMap[ExpenseType::NAME_OTHER] ?? null,
                                'expense_date' => $expense->created_at ? date('Y-m-d', strtotime((string) $expense->created_at)) : now()->toDateString(),
                            ]);
                    }
                });

            Schema::table('expenses', function (Blueprint $table): void {
                $table->dropColumn('expense_type');
            });
        } else {
            DB::table('expenses')
                ->whereNull('expense_date')
                ->update([
                    'expense_date' => now()->toDateString(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('expenses')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table): void {
            if (! Schema::hasColumn('expenses', 'expense_type')) {
                $table->string('expense_type', 50)->nullable();
            }
        });

        if (Schema::hasColumn('expenses', 'expense_type_id')) {
            DB::table('expenses')
                ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type_id')
                ->update([
                    'expense_type' => DB::raw('expense_types.name'),
                ]);

            Schema::table('expenses', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('expense_type_id');
            });
        }

        Schema::table('expenses', function (Blueprint $table): void {
            if (Schema::hasColumn('expenses', 'expense_date')) {
                $table->dropColumn('expense_date');
            }

            if (Schema::hasColumn('expenses', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
