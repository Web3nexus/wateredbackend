<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('calendar_months', function (Blueprint $table) {
            if (!Schema::hasColumn('calendar_months', 'season')) {
                $table->string('season')->nullable()->after('year');
            }
            if (!Schema::hasColumn('calendar_months', 'gregorian_reference')) {
                $table->string('gregorian_reference')->nullable()->after('season');
            }
        });
    }

    public function down(): void
    {
        Schema::table('calendar_months', function (Blueprint $table) {
            $table->dropColumn(['season', 'gregorian_reference']);
        });
    }
};
