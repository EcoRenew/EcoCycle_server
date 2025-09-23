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
        Schema::create('phones', function (Blueprint $table) {
            $table->id('phone_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('phone')->index();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();

            $table->unique(['user_id', 'phone'], 'phones_user_phone_unique');

            // explicit, unlikely-to-collide FK name
            $table->foreign('user_id', 'fk_phones_user_id_users_user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phones', function (Blueprint $table) {
            // drop the FK by the explicit name first
            $table->dropForeign('fk_phones_user_id_users_user_id');
        });

        Schema::dropIfExists('phones');
    }
};
