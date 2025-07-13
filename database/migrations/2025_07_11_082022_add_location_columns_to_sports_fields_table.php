<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('sports_fields', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable();
            }

            if (!Schema::hasColumn('sports_fields', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sports_fields', function (Blueprint $table) {
            if (Schema::hasColumn('sports_fields', 'latitude')) {
                $table->dropColumn('latitude');
            }

            if (Schema::hasColumn('sports_fields', 'longitude')) {
                $table->dropColumn('longitude');
            }
        });
    }
};
