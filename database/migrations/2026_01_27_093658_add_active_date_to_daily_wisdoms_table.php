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
        Schema::table('daily_wisdoms', function (Blueprint $table) {
            $table->date('active_date')->nullable()->after('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_wisdoms', function (Blueprint $table) {
            $table->dropColumn('active_date');
        });
    }
};
