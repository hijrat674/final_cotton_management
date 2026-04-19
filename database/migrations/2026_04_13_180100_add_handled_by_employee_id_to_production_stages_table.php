<?php

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
        Schema::table('production_stages', function (Blueprint $table) {
            $table->foreignId('handled_by_employee_id')
                ->nullable()
                ->after('handled_by')
                ->constrained('employees')
                ->restrictOnDelete();
        });

        $users = DB::table('production_stages')
            ->whereNotNull('handled_by')
            ->select('handled_by')
            ->distinct()
            ->pluck('handled_by');

        foreach ($users as $userId) {
            $user = DB::table('users')->where('id', $userId)->first();

            if (! $user) {
                continue;
            }

            $employeeId = DB::table('employees')->where('user_id', $userId)->value('id');

            if (! $employeeId) {
                $employeeId = DB::table('employees')->insertGetId([
                    'user_id' => $userId,
                    'full_name' => $user->name,
                    'phone' => 'N/A',
                    'position' => 'Production Handler',
                    'department' => 'Production',
                    'salary' => 0,
                    'hire_date' => now()->toDateString(),
                    'address' => null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('production_stages')
                ->where('handled_by', $userId)
                ->update(['handled_by_employee_id' => $employeeId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_stages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handled_by_employee_id');
        });
    }
};
