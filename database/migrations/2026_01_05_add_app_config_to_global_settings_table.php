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
            if (!Schema::hasColumn('global_settings', 'primary_color')) {
                $table->string('primary_color')->default('#6366f1')->after('logo_path');
            }
            if (!Schema::hasColumn('global_settings', 'secondary_color')) {
                $table->string('secondary_color')->default('#8b5cf6')->after('primary_color');
            }
            if (!Schema::hasColumn('global_settings', 'default_language')) {
                $table->string('default_language')->default('en')->after('secondary_color');
            }
            if (!Schema::hasColumn('global_settings', 'supported_languages')) {
                $table->json('supported_languages')->nullable()->after('default_language');
            }
            if (!Schema::hasColumn('global_settings', 'maintenance_mode')) {
                $table->boolean('maintenance_mode')->default(false)->after('supported_languages');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'default_language',
                'supported_languages',
                'maintenance_mode',
            ]);
        });
    }
};
