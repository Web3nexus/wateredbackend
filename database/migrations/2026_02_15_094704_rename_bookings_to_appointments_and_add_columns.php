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
        Schema::rename('bookings', 'appointments');

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('appointment_code')->unique()->after('id');
            $table->string('full_name')->nullable()->after('user_id');
            $table->string('email')->nullable()->after('full_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('service_type')->nullable()->after('consultation_type_id');
            $table->decimal('amount', 10, 2)->default(0)->after('notes');
            $table->string('payment_status')->default('pending')->after('amount'); // pending, paid, failed
            $table->string('payment_reference')->nullable()->after('payment_status');

            // Rename status to appointment_status or keep it? 
            // The prompt specifically asked for appointment_status.
            $table->renameColumn('status', 'appointment_status');

            // Make user_id nullable for guest bookings
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->renameColumn('appointment_status', 'status');
            $table->dropColumn([
                'appointment_code',
                'full_name',
                'email',
                'phone',
                'service_type',
                'amount',
                'payment_status',
                'payment_reference'
            ]);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::rename('appointments', 'bookings');
    }
};
