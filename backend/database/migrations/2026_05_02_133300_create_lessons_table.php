<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('app_module');
            $table->string('title');
            $table->text('description');
            $table->integer('order')->default(1);
            $table->integer('duration_min')->default(15);
            $table->string('difficulty');
            $table->json('objectives');
            $table->json('mos_objectives');
            $table->json('content_json');
            $table->string('thumbnail_url')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['app_module', 'order']);
            $table->index(['app_module', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
