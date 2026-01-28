<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_ngn', 10, 2)->nullable()->after('price');
            $table->decimal('price_usd', 10, 2)->nullable()->after('price_ngn');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_ngn', 'price_usd']);
        });
    }
};
