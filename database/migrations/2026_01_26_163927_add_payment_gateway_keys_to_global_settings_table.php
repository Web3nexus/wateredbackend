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
            $table->string('stripe_public_key')->nullable()->after('is_ads_enabled');
            $table->string('stripe_secret_key')->nullable()->after('stripe_public_key');
            $table->string('paystack_public_key')->nullable()->after('stripe_secret_key');
            $table->string('paystack_secret_key')->nullable()->after('paystack_public_key');
            $table->string('flutterwave_public_key')->nullable()->after('paystack_secret_key');
            $table->string('flutterwave_secret_key')->nullable()->after('flutterwave_public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_public_key',
                'stripe_secret_key',
                'paystack_public_key',
                'paystack_secret_key',
                'flutterwave_public_key',
                'flutterwave_secret_key',
            ]);
        });
    }
};
