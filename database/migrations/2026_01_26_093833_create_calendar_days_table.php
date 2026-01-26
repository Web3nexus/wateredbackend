<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calendar_days', function (Blueprint $table) {
            $table->id();
            $table->integer('month'); // 1-13 (Kemetic has 13 months)
            $table->integer('day'); // 1-30
            $table->string('month_name'); // e.g., "Thoth", "Phaophi"
            $table->string('day_name')->nullable(); // Special day name
            $table->text('description')->nullable();
            $table->json('associated_deities')->nullable(); // Array of deity names
            $table->string('celebration_type')->nullable(); // e.g., "Festival", "Sacred Day"
            $table->boolean('is_special_day')->default(false);
            $table->string('color')->nullable(); // Hex color for UI
            $table->timestamps();

            $table->unique(['month', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_days');
    }
};
