<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('paystack_subscription_code')->nullable()->index()->after('original_transaction_id');
            $table->string('paystack_email_token')->nullable()->after('paystack_subscription_code');
            $table->string('failure_reason', 500)->nullable()->after('amount');
            $table->boolean('auto_renews')->default(false)->after('failure_reason');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'paystack_subscription_code',
                'paystack_email_token',
                'failure_reason',
                'auto_renews',
            ]);
        });
    }
};
