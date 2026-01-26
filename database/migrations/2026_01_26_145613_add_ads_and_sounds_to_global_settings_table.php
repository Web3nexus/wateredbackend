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
            $table->boolean('is_ads_enabled')->default(false)->after('maintenance_mode');
            $table->string('ad_unit_id_android')->nullable()->after('is_ads_enabled');
            $table->string('ad_unit_id_ios')->nullable()->after('ad_unit_id_android');
            $table->json('ads_screens')->nullable()->after('ad_unit_id_ios');
            $table->string('notification_sound_path')->nullable()->after('ads_screens');
            $table->string('alarm_sound_path')->nullable()->after('notification_sound_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_ads_enabled',
                'ad_unit_id_android',
                'ad_unit_id_ios',
                'ads_screens',
                'notification_sound_path',
                'alarm_sound_path',
            ]);
        });
    }
};
