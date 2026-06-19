<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_categories', function (Blueprint $table) {
            $table->json('availability')->nullable()->after('is_free');
            $table->dropColumn('price');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_categories', function (Blueprint $table) {
            $table->dropColumn('availability');
            $table->decimal('price', 10, 2)->nullable()->after('is_free');
        });
    }
};
