<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('original_transaction_id')->nullable()->after('provider_subscription_id');
            $table->text('raw_provider_event')->nullable()->after('expires_at');
            $table->dateTime('processed_at')->nullable()->after('raw_provider_event');
            $table->unique('provider_subscription_id', 'subscriptions_provider_subscription_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique('subscriptions_provider_subscription_unique');
            $table->dropColumn(['original_transaction_id', 'raw_provider_event', 'processed_at']);
        });
    }
};
