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
            $table->boolean('is_rituals_enabled')->default(true);
            $table->boolean('is_teachings_enabled')->default(true);
            $table->boolean('is_audios_enabled')->default(true);
            $table->boolean('is_nima_sedani_enabled')->default(true);
            $table->boolean('is_community_enabled')->default(true);

            $table->boolean('is_rituals_premium_only')->default(false);
            $table->boolean('is_teachings_premium_only')->default(false);
            $table->boolean('is_audios_premium_only')->default(false);
            $table->boolean('is_nima_sedani_premium_only')->default(false);

            $table->integer('app_config_version')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_rituals_enabled',
                'is_teachings_enabled',
                'is_audios_enabled',
                'is_nima_sedani_enabled',
                'is_community_enabled',
                'is_rituals_premium_only',
                'is_teachings_premium_only',
                'is_audios_premium_only',
                'is_nima_sedani_premium_only',
                'app_config_version',
            ]);
        });
    }
};
