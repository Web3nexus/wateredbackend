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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan_id'); // e.g. monthly_premium
            $table->string('provider'); // apple, google, stripe
            $table->string('provider_subscription_id');
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->dateTime('starts_at');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        // Add is_premium column to users for easy access
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });
        Schema::dropIfExists('subscriptions');
    }
};
