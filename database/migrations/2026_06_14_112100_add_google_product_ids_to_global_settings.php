<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('google_monthly_product_id')->nullable()->after('google_play_service_account_json');
            $table->string('google_yearly_product_id')->nullable()->after('google_monthly_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'google_monthly_product_id',
                'google_yearly_product_id',
            ]);
        });
    }
};
