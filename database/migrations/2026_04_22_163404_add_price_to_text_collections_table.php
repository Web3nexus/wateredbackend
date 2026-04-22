<?php
/* File: database/migrations/2026_04_22_163404_add_price_to_text_collections_table.php */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('text_collections', function (Blueprint $table) {
            // Price in Naira decimal (e.g., 1500.00)
            $table->decimal('price', 10, 2)->default(0.00)->after('is_premium');
        });
    }

    public function down(): void
    {
        Schema::table('text_collections', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
