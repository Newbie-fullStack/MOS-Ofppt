<?php
// ══════════════════════════════════════════════════════════════════
// TOUTES LES MIGRATIONS — MOS OFPPT
// Copier chaque bloc dans un fichier séparé dans database/migrations/
// Nommage : YYYY_MM_DD_HHMMSS_create_{table}_table.php
// Ordre : respecter les dépendances (FK)
// ══════════════════════════════════════════════════════════════════

// ── 1. users ──────────────────────────────────────────────────────
// Fichier : 2024_01_01_000001_create_users_table.php

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('password');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('role')->default('STUDENT');   // STUDENT | TRAINER | ADMIN
    $table->string('avatar_url')->nullable();
    $table->integer('xp_points')->default(0);
    $table->integer('streak_days')->default(0);
    $table->timestamp('last_login_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();

    $table->index(['role', 'is_active']);
});

// ── 2. lessons ────────────────────────────────────────────────────
// Fichier : 2024_01_01_000002_create_lessons_table.php

Schema::create('lessons', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('app_module');           // WORD | EXCEL | POWERPOINT
    $table->string('title');
    $table->text('description');
    $table->integer('order')->default(1);
    $table->integer('duration_min')->default(15);
    $table->string('difficulty');           // BEGINNER | INTERMEDIATE | ADVANCED
    $table->json('objectives');             // ["objectif 1", "objectif 2"]
    $table->json('mos_objectives');         // ["1.1.1", "1.1.2"]
    $table->json('content_json');           // Blocs de contenu structurés
    $table->string('thumbnail_url')->nullable();
    $table->string('video_url')->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamps();

    $table->index(['app_module', 'order']);
    $table->index(['app_module', 'is_published']);
});

// ── 3. exercises ──────────────────────────────────────────────────
// Fichier : 2024_01_01_000003_create_exercises_table.php

Schema::create('exercises', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
    $table->string('title');
    $table->text('description');
    $table->json('instructions');           // Étapes de l'exercice
    $table->string('file_url')->nullable(); // Chemin fichier .docx/.xlsx/.pptx
    $table->string('solution_url')->nullable();
    $table->string('difficulty');
    $table->integer('order')->default(1);
    $table->boolean('is_published')->default(false);
    $table->timestamps();
});

// ── 4. questions ──────────────────────────────────────────────────
// Fichier : 2024_01_01_000004_create_questions_table.php

Schema::create('questions', function (Blueprint $table) {
    $table->string('id')->primary();        // ID depuis le JSON ("word-q-001")
    $table->string('app_module');
    $table->string('domain');               // "styles", "formulas", "animations"
    $table->string('difficulty');
    $table->text('question_text');
    $table->json('options');                // Array de 4 options
    $table->unsignedTinyInteger('correct_index'); // 0, 1, 2 ou 3
    $table->text('explanation');
    $table->string('mos_objective')->nullable(); // "1.1.1"
    $table->string('image_url')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['app_module', 'domain']);
    $table->index(['app_module', 'difficulty']);
});

// ── 5. quizzes ────────────────────────────────────────────────────
// Fichier : 2024_01_01_000005_create_quizzes_table.php

Schema::create('quizzes', function (Blueprint $table) {
    $table->string('id')->primary();        // ID sémantique : "quiz-word-full"
    $table->string('app_module');
    $table->string('title');
    $table->text('description');
    $table->integer('duration_min')->default(15);
    $table->integer('passing_score')->default(70); // En %
    $table->boolean('is_exam_mode')->default(false);
    $table->boolean('is_published')->default(true);
    $table->timestamps();

    $table->index(['app_module', 'is_exam_mode']);
});

// ── 6. quiz_questions (pivot) ────────────────────────────────────
// Fichier : 2024_01_01_000006_create_quiz_questions_table.php

Schema::create('quiz_questions', function (Blueprint $table) {
    $table->string('quiz_id');
    $table->string('question_id');
    $table->integer('order')->default(1);

    $table->primary(['quiz_id', 'question_id']);
    $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
    $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
});

// ── 7. quiz_attempts ──────────────────────────────────────────────
// Fichier : 2024_01_01_000007_create_quiz_attempts_table.php

Schema::create('quiz_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('quiz_id');
    $table->foreign('quiz_id')->references('id')->on('quizzes')->cascadeOnDelete();
    $table->integer('score');                   // En %
    $table->integer('total_questions');
    $table->integer('correct_questions');
    $table->json('answers');                    // { "word-q-001": 1, "word-q-002": 0, ... }
    $table->integer('duration_sec');
    $table->boolean('passed');
    $table->timestamp('completed_at')->useCurrent();
    $table->timestamps();

    $table->index(['user_id', 'quiz_id']);
    $table->index(['user_id', 'passed']);
});

// ── 8. exam_attempts ──────────────────────────────────────────────
// Fichier : 2024_01_01_000008_create_exam_attempts_table.php

Schema::create('exam_attempts', function (Blueprint $table) {
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

// ── 9. progress ───────────────────────────────────────────────────
// Fichier : 2024_01_01_000009_create_progress_table.php

Schema::create('progress', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
    $table->boolean('completed')->default(false);
    $table->timestamp('completed_at')->nullable();
    $table->integer('time_spent_sec')->default(0);
    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

    $table->primary(['user_id', 'lesson_id']);
});

// ── 10. enrollments ───────────────────────────────────────────────
// Fichier : 2024_01_01_000010_create_enrollments_table.php

Schema::create('enrollments', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('app_module');
    $table->timestamp('enrolled_at')->useCurrent();
    $table->date('target_date')->nullable();
    $table->timestamp('completed_at')->nullable();

    $table->primary(['user_id', 'app_module']);
});

// ── 11. badges ────────────────────────────────────────────────────
// Fichier : 2024_01_01_000011_create_badges_table.php

Schema::create('badges', function (Blueprint $table) {
    $table->string('id')->primary();        // "badge-word-certified"
    $table->string('name');
    $table->text('description');
    $table->string('icon_url');
    $table->string('condition');            // Code condition : "exam_pass_word"
    $table->integer('xp_reward')->default(50);
    $table->timestamps();
});

// ── 12. user_badges ───────────────────────────────────────────────
// Fichier : 2024_01_01_000012_create_user_badges_table.php

Schema::create('user_badges', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('badge_id');
    $table->foreign('badge_id')->references('id')->on('badges')->cascadeOnDelete();
    $table->timestamp('earned_at')->useCurrent();

    $table->primary(['user_id', 'badge_id']);
});

// ── 13. class_rooms ───────────────────────────────────────────────
// Fichier : 2024_01_01_000013_create_class_rooms_table.php
// Note : "class_rooms" et non "classes" (mot réservé PHP)

Schema::create('class_rooms', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
    $table->string('code')->unique(); // Code d'invitation 6 caractères
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// ── 14. class_members ─────────────────────────────────────────────
// Fichier : 2024_01_01_000014_create_class_members_table.php

Schema::create('class_members', function (Blueprint $table) {
    $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->timestamp('joined_at')->useCurrent();

    $table->primary(['class_room_id', 'user_id']);
});

// ── Sanctum personal_access_tokens est créé automatiquement par :
// php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
// php artisan migrate
