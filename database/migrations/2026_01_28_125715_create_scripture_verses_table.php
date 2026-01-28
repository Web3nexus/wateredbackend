<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scripture_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('scripture_chapters')->onDelete('cascade');
            $table->integer('verse_number');
            $table->text('content'); // the actual verse text
            $table->timestamps();

            $table->unique(['chapter_id', 'verse_number']);
            $table->index('chapter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scripture_verses');
    }
};
