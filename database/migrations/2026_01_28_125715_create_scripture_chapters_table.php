<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scripture_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('scripture_books')->onDelete('cascade');
            $table->integer('chapter_number');
            $table->string('title')->nullable(); // e.g., "The Beginning"
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'chapter_number']);
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scripture_chapters');
    }
};
