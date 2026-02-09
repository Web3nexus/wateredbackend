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
        $tables = ['text_collections', 'deities', 'audios', 'scripture_books', 'videos'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'tradition_id')) {
                        $table->unsignedBigInteger('tradition_id')->nullable()->change();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversing this might fail if there are null values, so we generally leave it or handle carefully.
        // For now, we won't strictly reverse it to NOT NULL to avoid data loss issues during rollback if data exists.
    }
};
