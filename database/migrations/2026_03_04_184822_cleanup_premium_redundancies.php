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
        Schema::table('incantations', function (Blueprint $table) {
            if (Schema::hasColumn('incantations', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
            if (Schema::hasColumn('incantations', 'is_premium')) {
                $table->dropColumn('is_premium');
            }
        });

        Schema::table('rituals', function (Blueprint $table) {
            if (Schema::hasColumn('rituals', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
            // is_premium is kept for rituals as it will be the single source of truth.
        });

        Schema::table('global_settings', function (Blueprint $table) {
            if (Schema::hasColumn('global_settings', 'is_incantations_premium_only')) {
                $table->dropColumn('is_incantations_premium_only');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incantations', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_premium')->default(false);
        });

        Schema::table('rituals', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false);
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->boolean('is_incantations_premium_only')->default(true);
        });
    }
};
