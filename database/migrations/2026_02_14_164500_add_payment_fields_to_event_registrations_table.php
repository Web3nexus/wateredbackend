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
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('status');
            $table->decimal('amount', 10, 2)->nullable()->after('payment_reference');
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending')->after('amount');
            $table->string('payment_method')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'amount', 'payment_status', 'payment_method']);
        });
    }
};
