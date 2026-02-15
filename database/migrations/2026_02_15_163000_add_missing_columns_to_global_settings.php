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
            // Images
            if (!Schema::hasColumn('global_settings', 'hero_image')) {
                $table->string('hero_image')->nullable()->after('hero_cta_text');
            }
            if (!Schema::hasColumn('global_settings', 'rituals_image')) {
                $table->string('rituals_image')->nullable()->after('ritual_witnesses_description');
            }
            if (!Schema::hasColumn('global_settings', 'about_image')) {
                $table->string('about_image')->nullable()->after('about_quote');
            }
            if (!Schema::hasColumn('global_settings', 'community_image')) {
                $table->string('community_image')->nullable()->after('rituals_image');
            }

            // Currency
            if (!Schema::hasColumn('global_settings', 'system_currency')) {
                $table->string('system_currency')->default('USD')->after('maintenance_mode');
            }
            if (!Schema::hasColumn('global_settings', 'currency_symbol')) {
                $table->string('currency_symbol')->default('$')->after('system_currency');
            }
            if (!Schema::hasColumn('global_settings', 'currency_position')) {
                $table->string('currency_position')->default('before')->after('currency_symbol'); // 'before' or 'after'
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
                'hero_image',
                'rituals_image',
                'system_currency',
                'currency_symbol',
                'currency_position',
            ]);
        });
    }
};
