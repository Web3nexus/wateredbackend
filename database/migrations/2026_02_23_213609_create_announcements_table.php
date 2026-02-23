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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content'); // Rich Text
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_published')->default(false);
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('push_enabled')->default(true);
            $table->string('target_audience')->default('all'); // all, premium
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
