<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('quiz_id');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
            $table->string('app_module');
            $table->string('status')->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->json('integrity_logs')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'app_module', 'status']);
        });

        Schema::table('exam_attempts', function (Blueprint $table): void {
            $table->uuid('exam_session_id')->nullable()->after('user_id');
            $table->json('integrity_logs')->nullable()->after('answers');
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table): void {
            $table->dropColumn(['exam_session_id', 'integrity_logs']);
        });

        Schema::dropIfExists('exam_sessions');
    }
};
