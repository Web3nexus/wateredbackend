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
        Schema::table('faqs', function (Blueprint $table) {
            // Rename 'order' to 'sort_order' if it exists
            if (Schema::hasColumn('faqs', 'order') && !Schema::hasColumn('faqs', 'sort_order')) {
                $table->renameColumn('order', 'sort_order');
            }
            // If 'order' doesn't exist and 'sort_order' doesn't exist, create 'sort_order'
            elseif (!Schema::hasColumn('faqs', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('answer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (Schema::hasColumn('faqs', 'sort_order')) {
                $table->renameColumn('sort_order', 'order');
            }
        });
    }
};
