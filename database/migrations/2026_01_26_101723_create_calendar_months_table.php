<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('calendar_days');
        Schema::dropIfExists('calendar_months');

        Schema::create('calendar_months', function (Blueprint $table) {
            $table->id();
            $table->integer('number'); // 1-13
            $table->string('standard_name'); // e.g. Thoth
            $table->string('custom_name')->nullable(); // Admin custom name
            $table->string('year')->nullable(); // e.g. 6262
            $table->string('season')->nullable(); // Akhet, Peret, Shemu
            $table->string('gregorian_reference')->nullable(); // e.g. "July - August"
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('calendar_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_month_id')->constrained()->cascadeOnDelete();
            $table->integer('day_number'); // 1-30
            $table->string('custom_day_name')->nullable();
            $table->string('gregorian_day')->nullable(); // e.g., "July 19"
            $table->text('content')->nullable(); // For celebrations, gods, etc.
            $table->json('associated_deities')->nullable();
            $table->string('celebration_type')->nullable();
            $table->boolean('is_sacred')->default(false);
            $table->string('ui_color')->nullable();
            $table->timestamps();

            $table->unique(['calendar_month_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_days');
        Schema::dropIfExists('calendar_months');
    }
};
