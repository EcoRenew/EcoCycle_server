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
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();     
            $table->string('default_unit')->nullable();   
            $table->json('units')->nullable(); 
            $table->decimal('price_per_unit', 10, 2);
            $table->decimal('points_per_kg', 8, 2)->nullable();
            $table->foreignId('category_id')->constrained('categories','category_id')->index();
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
