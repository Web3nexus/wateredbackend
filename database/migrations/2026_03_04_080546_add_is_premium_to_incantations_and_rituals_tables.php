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
        if (!Schema::hasColumn('incantations', 'is_premium')) {
            Schema::table('incantations', function (Blueprint $table) {
                $table->boolean('is_premium')->default(false)->after('audio_url');
            });
        }

        if (!Schema::hasColumn('rituals', 'is_premium')) {
            Schema::table('rituals', function (Blueprint $table) {
                $table->boolean('is_premium')->default(false)->after('is_sacred_daily');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incantations', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });

        Schema::table('rituals', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });
    }
};
