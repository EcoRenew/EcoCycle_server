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
        Schema::create('donations', function (Blueprint $table) {
            $table->id('donation_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pickup_address_id')->nullable();
            $table->string('item_category');
            $table->json('photos')->nullable();
            $table->string('condition');
            $table->text('description');
            $table->date('pickup_date');
            $table->text('additional_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('pickup_address_id')->references('address_id')->on('addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
