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
            $table->string('premium_monthly_price')->nullable()->after('premium_yearly_id');
            $table->string('premium_yearly_price')->nullable()->after('premium_monthly_price');
            $table->integer('premium_monthly_amount')->nullable()->after('premium_yearly_price');
            $table->integer('premium_yearly_amount')->nullable()->after('premium_monthly_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'premium_monthly_price',
                'premium_yearly_price',
                'premium_monthly_amount',
                'premium_yearly_amount'
            ]);
        });
    }
};
