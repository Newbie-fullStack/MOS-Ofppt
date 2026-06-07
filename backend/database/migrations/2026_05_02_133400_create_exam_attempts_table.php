<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('app_module');
            $table->integer('score');
            $table->integer('total_questions');
            $table->integer('correct_questions');
            $table->json('answers');
            $table->boolean('passed');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->useCurrent();
            $table->integer('duration_sec');
            $table->timestamps();
            $table->index(['user_id', 'app_module']);
            $table->index(['user_id', 'passed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
