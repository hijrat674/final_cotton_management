<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cotton_entries', function (Blueprint $table) {
            $table->id();
            $table->string('truck_number');
            $table->string('driver_name');
            $table->string('driver_phone', 20)->nullable();
            $table->decimal('gross_weight', 15, 3);
            $table->decimal('tare_weight', 15, 3);
            $table->decimal('net_weight', 15, 3);
            $table->date('entry_date')->index();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotton_entries');
    }
};
