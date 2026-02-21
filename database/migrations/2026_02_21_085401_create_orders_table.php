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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'closed', 'invite_only'])->default('open');
            $table->string('cta_text')->default('Apply Now');
            $table->string('cta_link')->nullable();
            $table->enum('action_type', ['external_link', 'internal_route', 'application_form'])->default('application_form');
            $table->string('image_url')->nullable();
            $table->integer('order_level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
