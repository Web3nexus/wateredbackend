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
        Schema::table('deities', function (Blueprint $table) {
            if (!Schema::hasColumn('deities', 'origin'))
                $table->string('origin')->nullable();
            if (!Schema::hasColumn('deities', 'mythology_story'))
                $table->text('mythology_story')->nullable();
            if (!Schema::hasColumn('deities', 'symbols'))
                $table->text('symbols')->nullable();
            if (!Schema::hasColumn('deities', 'domains'))
                $table->text('domains')->nullable();
            if (!Schema::hasColumn('deities', 'sacred_elements'))
                $table->text('sacred_elements')->nullable();
        });

        Schema::table('rituals', function (Blueprint $table) {
            if (!Schema::hasColumn('rituals', 'category'))
                $table->string('category')->nullable();
            if (!Schema::hasColumn('rituals', 'steps'))
                $table->json('steps')->nullable();
            if (!Schema::hasColumn('rituals', 'time_of_day'))
                $table->time('time_of_day')->nullable();
            if (!Schema::hasColumn('rituals', 'symbolic_meaning'))
                $table->text('symbolic_meaning')->nullable();
        });

        Schema::table('incantations', function (Blueprint $table) {
            if (!Schema::hasColumn('incantations', 'category'))
                $table->string('category')->nullable();
            if (!Schema::hasColumn('incantations', 'spoken_text'))
                $table->text('spoken_text')->nullable();
            if (!Schema::hasColumn('incantations', 'intended_outcome'))
                $table->text('intended_outcome')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'recurrence'))
                $table->string('recurrence')->nullable(); // Weekly, Monthly, Yearly, Special
            if (!Schema::hasColumn('events', 'cultural_origin'))
                $table->string('cultural_origin')->nullable();
        });

        Schema::table('consultation_types', function (Blueprint $table) {
            if (!Schema::hasColumn('consultation_types', 'category'))
                $table->string('category')->nullable();
            if (!Schema::hasColumn('consultation_types', 'price'))
                $table->decimal('price', 10, 2)->default(0);
        });

        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('theme')->nullable();
                $table->date('date');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('audios', function (Blueprint $table) {
            if (!Schema::hasColumn('audios', 'category'))
                $table->string('category')->nullable();
        });

        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'category'))
                $table->string('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deities', function (Blueprint $table) {
            $table->dropColumn(['origin', 'mythology_story', 'symbols', 'domains', 'sacred_elements']);
        });

        Schema::table('rituals', function (Blueprint $table) {
            $table->dropColumn(['category', 'steps', 'time_of_day', 'symbolic_meaning']);
        });

        Schema::table('incantations', function (Blueprint $table) {
            $table->dropColumn(['category', 'spoken_text', 'intended_outcome']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['recurrence', 'cultural_origin']);
        });

        Schema::table('consultation_types', function (Blueprint $table) {
            $table->dropColumn(['category', 'price']);
        });

        Schema::dropIfExists('holidays');

        Schema::table('audios', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
