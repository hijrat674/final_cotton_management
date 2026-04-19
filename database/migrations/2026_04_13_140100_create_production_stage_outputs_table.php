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
        Schema::create('production_stage_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_stage_id')->constrained('production_stages')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->string('output_type', 50)->index();
            $table->decimal('quantity', 15, 3);
            $table->string('unit', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_stage_outputs');
    }
};
