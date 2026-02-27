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
            $table->boolean('is_premium')->default(false)->after('is_sacred_daily');
        });

        Schema::table('audios', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('is_featured');
        });

        Schema::table('teachings', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('is_published');
        });

        Schema::table('text_collections', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rituals', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });

        Schema::table('audios', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });

        Schema::table('teachings', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });

        Schema::table('text_collections', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });
    }
};
