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
            $table->string('premium_monthly_id')->nullable()->after('flutterwave_secret_key');
            $table->string('premium_yearly_id')->nullable()->after('premium_monthly_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['premium_monthly_id', 'premium_yearly_id']);
        });
    }
};
