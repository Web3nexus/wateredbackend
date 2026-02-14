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
        Schema::create('holiday_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('holiday_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('calendar_day_id')->nullable()->constrained('calendar_days')->onDelete('cascade');
            $table->string('holiday_name')->nullable();
            $table->datetime('reminder_time');
            $table->string('reminder_type')->default('custom'); // day_of, 24h_before, custom
            $table->string('status')->default('active');
            $table->datetime('notified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_reminders');
    }
};
