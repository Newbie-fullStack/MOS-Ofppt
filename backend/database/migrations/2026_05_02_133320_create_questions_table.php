<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('app_module');
            $table->string('domain');
            $table->string('difficulty');
            $table->text('question_text');
            $table->json('options');
            $table->unsignedTinyInteger('correct_index');
            $table->text('explanation');
            $table->string('mos_objective')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['app_module', 'domain']);
            $table->index(['app_module', 'difficulty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
