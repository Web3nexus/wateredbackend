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
        Schema::create('daily_wisdoms', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('author')->nullable();
            $table->string('background_image_url')->nullable();
            $table->date('publish_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_wisdoms');
    }
};
