<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'tradition_id')) {
                $table->foreignId('tradition_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('events', 'category')) {
                $table->string('category')->nullable();
            }
        });

        Schema::table('rituals', function (Blueprint $table) {
            if (!Schema::hasColumn('rituals', 'tradition_id')) {
                $table->foreignId('tradition_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['tradition_id']);
            $table->dropColumn(['tradition_id', 'category']);
        });

        Schema::table('rituals', function (Blueprint $table) {
            $table->dropForeign(['tradition_id']);
            $table->dropColumn('tradition_id');
        });
    }
};
