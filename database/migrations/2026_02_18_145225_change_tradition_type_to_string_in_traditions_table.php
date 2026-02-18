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
        Schema::table('traditions', function (Blueprint $table) {
            $table->string('tradition_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traditions', function (Blueprint $table) {
            $table->enum('tradition_type', ['scripture', 'history', 'deity'])->default('scripture')->change();
        });
    }
};
