<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->integer('verse_number')->nullable()->after('chapter_id');
            $table->index(['chapter_id', 'verse_number']);
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('verse_number');
        });
    }
};
