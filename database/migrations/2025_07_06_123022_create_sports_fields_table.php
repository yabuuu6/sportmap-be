<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->float('rating')->default(0); 
        });
    }

    public function down(): void
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'latitude', 'longitude', 'is_verified', 'rating']);
        });
    }
};
