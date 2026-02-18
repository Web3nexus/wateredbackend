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
        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'scheduled_at')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->renameColumn('scheduled_at', 'start_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'start_time')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->renameColumn('start_time', 'scheduled_at');
            });
        }
    }
};
