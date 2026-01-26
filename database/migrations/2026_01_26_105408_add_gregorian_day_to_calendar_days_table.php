<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('calendar_days', function (Blueprint $table) {
            if (!Schema::hasColumn('calendar_days', 'gregorian_day')) {
                $table->string('gregorian_day')->nullable()->after('day_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calendar_days', function (Blueprint $table) {
            $table->dropColumn('gregorian_day');
        });
    }
};
