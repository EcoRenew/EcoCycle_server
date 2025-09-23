<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id('email_log_id');
            $table->unsignedBigInteger('request_id');
            $table->string('email_type');
            $table->string('to_email');
            $table->string('subject')->nullable();
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');
            $table->index(['request_id', 'email_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
