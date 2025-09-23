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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->datetime('invoice_date');
            $table->decimal('total_amount', 10, 2);
            $table->unsignedBigInteger('request_id')->unique();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');

            $table->unsignedBigInteger('donation_id')->nullable();
            $table->foreign('donation_id')->references('donation_id')->on('donations')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
