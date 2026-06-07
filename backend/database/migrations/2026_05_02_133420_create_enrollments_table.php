<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table): void {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('app_module');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->date('target_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->primary(['user_id', 'app_module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
