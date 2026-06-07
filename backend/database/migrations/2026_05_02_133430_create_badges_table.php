<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->string('icon_url');
            $table->string('condition');
            $table->integer('xp_reward')->default(50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
