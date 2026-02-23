<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign key constraint on comments table
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                // Drop the foreign key if it exists
                $table->dropForeign(['post_id']);
                // Also drop the column as it's specifically for Posts
                $table->dropColumn('post_id');
            });
        }

        // 2. Clean up polymorphic likes/comments for Posts
        DB::table('comments')->where('commentable_type', 'App\Models\Post')->delete();
        DB::table('likes')->where('likeable_type', 'App\Models\Post')->delete();

        // Also cleanup post_likes if it exists as a separate table
        if (Schema::hasTable('post_likes')) {
            Schema::dropIfExists('post_likes');
        }

        // 3. Drop the posts table
        Schema::dropIfExists('posts');
    }

    public function down(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->json('media_urls')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->string('status')->default('published');
            $table->integer('comments_count')->default(0);
            $table->timestamps();
        });
    }
};
