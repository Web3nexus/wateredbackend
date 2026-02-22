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
        Schema::table('rituals', function (Blueprint $table) {
            $table->boolean('is_sacred_daily')->default(false)->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rituals', function (Blueprint $table) {
            $table->dropColumn('is_sacred_daily');
        });
    }
};
