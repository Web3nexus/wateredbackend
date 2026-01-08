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
        // Drop old backup table if it exists
        if (Schema::hasTable('chapters_old')) {
            Schema::drop('chapters_old');
        }

        // Rename current chapters table if it exists
        if (Schema::hasTable('chapters')) {
            Schema::rename('chapters', 'chapters_old');
        }

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('number');
            $table->text('description')->nullable();
            $table->foreignId('collection_id')->constrained('text_collections')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['collection_id', 'number']);
            $table->unique(['collection_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');

        // Restore old chapters table if it existed
        if (Schema::hasTable('chapters_old')) {
            Schema::rename('chapters_old', 'chapters');
        }
    }
};
