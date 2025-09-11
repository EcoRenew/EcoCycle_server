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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->string('material_name');
            $table->decimal('price_per_unit', 10, 2);
            $table->enum('unit', ['kg', 'item'])->default('kg');
            $table->foreignId('category_id')->constrained('categories','category_id');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
