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
            $table->string('premium_title')->nullable()->after('premium_yearly_amount_usd');
            $table->string('premium_subtitle')->nullable()->after('premium_title');
            $table->json('premium_features')->nullable()->after('premium_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['premium_title', 'premium_subtitle', 'premium_features']);
        });
    }
};
