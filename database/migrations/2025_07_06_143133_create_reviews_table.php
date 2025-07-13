<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('sports_field_id');
            $table->foreign('sports_field_id')->references('id')->on('sports_fields')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->tinyInteger('rating');
            $table->timestamps();
            $table->unique(['user_id', 'sports_field_id']);
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
