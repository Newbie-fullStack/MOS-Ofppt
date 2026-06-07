<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('app_module');
            $table->string('title');
            $table->text('description');
            $table->integer('duration_min')->default(15);
            $table->integer('passing_score')->default(70);
            $table->boolean('is_exam_mode')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['app_module', 'is_exam_mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
