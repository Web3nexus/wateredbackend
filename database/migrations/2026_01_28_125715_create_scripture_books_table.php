<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scripture_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tradition_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Numasedan Book 1", "The Four Witnesses"
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // for sorting
            $table->string('image_url')->nullable(); // cover image
            $table->timestamps();

            $table->index(['tradition_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scripture_books');
    }
};
