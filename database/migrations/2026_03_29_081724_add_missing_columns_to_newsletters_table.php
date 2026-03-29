<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->longText('content')->after('subject');
            $table->string('recipients_type')->default('all')->after('content');
            $table->string('status')->default('draft')->after('recipients_type');
            $table->timestamp('sent_at')->nullable()->after('status');
            $table->integer('batch_delay')->default(1)->after('sent_at'); // seconds to wait between batches of 100
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn(['content', 'recipients_type', 'status', 'sent_at', 'batch_delay']);
        });
    }
};
