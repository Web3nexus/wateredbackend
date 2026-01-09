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
        Schema::table('audios', function (Blueprint $table) {
            $table->index(['is_active', 'published_at']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->index(['is_active', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audios', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'published_at']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'published_at']);
        });
    }
};
