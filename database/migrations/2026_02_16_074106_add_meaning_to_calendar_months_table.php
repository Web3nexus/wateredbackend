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
        Schema::table('calendar_months', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->string('meaning')->nullable()->after('deities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_months', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('meaning');
        });
    }
};
