<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('calendar_days', function (Blueprint $table) {
            $table->json('activities')->nullable();
            $table->json('restrictions')->nullable();
        });

        Schema::table('calendar_months', function (Blueprint $table) {
            $table->string('deities')->nullable(); // For Month-level deities
        });
    }

    public function down(): void
    {
        Schema::table('calendar_days', function (Blueprint $table) {
            $table->dropColumn(['activities', 'restrictions']);
        });

        Schema::table('calendar_months', function (Blueprint $table) {
            $table->dropColumn(['deities']);
        });
    }
};
