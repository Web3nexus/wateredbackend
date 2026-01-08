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
        Schema::create('entry_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->onDelete('cascade');
            $table->string('language_code', 10);
            $table->text('text');
            $table->string('translator_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['entry_id', 'language_code']);
            $table->unique(['entry_id', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_translations');
    }
};
