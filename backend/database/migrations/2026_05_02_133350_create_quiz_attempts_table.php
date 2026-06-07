<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('quiz_id');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
            $table->integer('score');
            $table->integer('total_questions');
            $table->integer('correct_questions');
            $table->json('answers');
            $table->integer('duration_sec');
            $table->boolean('passed');
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();
            $table->index(['user_id', 'quiz_id']);
            $table->index(['user_id', 'passed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
