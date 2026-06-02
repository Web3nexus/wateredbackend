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
            if (!Schema::hasColumn('newsletters', 'content')) {
                $table->longText('content')->after('subject');
            }
            if (!Schema::hasColumn('newsletters', 'recipients_type')) {
                $table->string('recipients_type')->default('all')->after('content');
            }
            if (!Schema::hasColumn('newsletters', 'status')) {
                $table->string('status')->default('draft')->after('recipients_type');
            }
            if (!Schema::hasColumn('newsletters', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('newsletters', 'batch_delay')) {
                $table->integer('batch_delay')->default(1)->after('sent_at');
            }
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
