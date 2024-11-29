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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('img_url');
            $table->string('audio_url');
            $table->string('duration');
            $table->string('posted_on');
            $table->integer('season');
            $table->integer('episode');
            $table->string('spotify_url');
            $table->string('apple_podcasts_url')->nullable();
            $table->char('archive', 2)->default('0');
            $table->char('featured', 2)->default('0');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
