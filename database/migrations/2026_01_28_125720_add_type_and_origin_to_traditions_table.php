<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('traditions', function (Blueprint $table) {
            $table->enum('tradition_type', ['scripture', 'history', 'deity'])->default('scripture')->after('description');
            $table->enum('african_origin', ['igbo', 'yoruba', 'kemetic', 'kush', 'akan', 'zulu', 'other'])->nullable()->after('tradition_type');
            $table->string('deity_image_url')->nullable()->after('african_origin');
        });
    }

    public function down(): void
    {
        Schema::table('traditions', function (Blueprint $table) {
            $table->dropColumn(['tradition_type', 'african_origin', 'deity_image_url']);
        });
    }
};
