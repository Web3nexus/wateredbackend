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
            if (!Schema::hasColumn('global_settings', 'is_landing_page_enabled')) {
                $table->boolean('is_landing_page_enabled')->default(true)->after('site_name');
            }
            if (!Schema::hasColumn('global_settings', 'favicon_path')) {
                $table->string('favicon_path')->nullable()->after('logo_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['is_landing_page_enabled', 'favicon_path']);
        });
    }
};
