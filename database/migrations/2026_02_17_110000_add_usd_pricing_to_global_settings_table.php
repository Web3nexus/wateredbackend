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
            $table->string('premium_monthly_price_usd')->nullable()->after('premium_yearly_amount');
            $table->string('premium_yearly_price_usd')->nullable()->after('premium_monthly_price_usd');
            $table->integer('premium_monthly_amount_usd')->nullable()->after('premium_yearly_price_usd');
            $table->integer('premium_yearly_amount_usd')->nullable()->after('premium_monthly_amount_usd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'premium_monthly_price_usd',
                'premium_yearly_price_usd',
                'premium_monthly_amount_usd',
                'premium_yearly_amount_usd'
            ]);
        });
    }
};
