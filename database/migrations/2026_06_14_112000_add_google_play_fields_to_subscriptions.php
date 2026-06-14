<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->text('google_purchase_token')->nullable()->after('paystack_email_token');
            $table->string('google_order_id', 128)->nullable()->index()->after('google_purchase_token');
            $table->string('google_product_id', 256)->nullable()->after('google_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'google_purchase_token',
                'google_order_id',
                'google_product_id',
            ]);
        });
    }
};
