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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('is_bookings_enabled');
            $table->boolean('is_app_bookings_enabled')->default(true)->after('is_community_enabled');
            $table->boolean('is_web_bookings_enabled')->default(true)->after('is_app_bookings_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['is_app_bookings_enabled', 'is_web_bookings_enabled']);
            $table->boolean('is_bookings_enabled')->default(true)->after('is_community_enabled');
        });
    }
};
