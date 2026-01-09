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
        Schema::table('videos', function (Blueprint $table) {
            $table->string('storage_url')->nullable()->after('youtube_url');
            $table->string('video_type')->default('youtube')->after('storage_url'); // 'youtube' or 'file'
            $table->string('youtube_url')->nullable()->change(); // Make youtube optional now
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['storage_url', 'video_type']);
            $table->string('youtube_url')->nullable(false)->change();
        });
    }
};
