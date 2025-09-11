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
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->enum('request_type', ['Donation', 'Recycling']);
            $table->enum('status', ['Pending', 'Assigned', 'Completed', 'Canceled'])->default('Pending');
            $table->datetime('pickup_date');
            $table->unsignedBigInteger('pickup_address_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('collector_id')->nullable();
            $table->timestamps();

            $table->foreign('pickup_address_id')->references('address_id')->on('addresses');
            $table->foreign('customer_id')->references('user_id')->on('users');
            $table->foreign('collector_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
