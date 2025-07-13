<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            // Tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('sports_fields', 'rating')) {
                $table->float('rating')->default(0);
            }

            if (!Schema::hasColumn('sports_fields', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            if (Schema::hasColumn('sports_fields', 'rating')) {
                $table->dropColumn('rating');
            }

            if (Schema::hasColumn('sports_fields', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
