<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('is_incantations_enabled')->default(true)->after('is_community_enabled');
            $table->boolean('is_incantations_premium_only')->default(false)->after('is_nima_sedani_premium_only');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['is_incantations_enabled', 'is_incantations_premium_only']);
        });
    }
};
