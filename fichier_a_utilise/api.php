<?php
// ══════════════════════════════════════════════════════════════════
// routes/api.php — MOS OFPPT
// Préfixe /api/v1 configuré dans bootstrap/app.php :
//   ->withRouting(api: __DIR__.'/../routes/api.php', apiPrefix: 'api/v1')
// ══════════════════════════════════════════════════════════════════

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ── Routes publiques ─────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login',           [AuthController::class, 'login'])
         ->middleware('throttle:5,1');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink']);
    Route::post('/reset-password',  [PasswordResetController::class, 'reset']);
});

// ── Routes protégées par Sanctum ─────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout',  [AuthController::class, 'logout']);
    Route::get('/user',          [AuthController::class, 'me']);
    Route::put('/user',          [AuthController::class, 'update']);
    Route::get('/user/stats',    [BadgeController::class, 'userStats']);
    Route::get('/user/badges',   [BadgeController::class, 'index']);

    // Modules & Leçons
    Route::get('/modules',                              [LessonController::class, 'modules']);
    Route::get('/modules/{module}/lessons',             [LessonController::class, 'index']);
    Route::get('/modules/{module}/lessons/{slug}',      [LessonController::class, 'show'])
         ->name('*.lessons.show');
    Route::post('/modules/{module}/enroll',             [EnrollmentController::class, 'store']);

    // Exercices
    Route::get('/modules/{module}/exercises',           [ExerciseController::class, 'index']);
    Route::get('/exercises/{exercise}/download',        [ExerciseController::class, 'download']);

    // Quiz
    Route::get('/quizzes/{module}',                     [QuizController::class, 'index']);
    Route::get('/quizzes/{quiz}',                       [QuizController::class, 'show']);
    Route::post('/quizzes/{quiz}/attempt',              [QuizController::class, 'attempt']);

    // Examen blanc
    Route::get('/exam/{module}',                        [ExamController::class, 'start']);
    Route::post('/exam/{module}/submit',                [ExamController::class, 'submit']);

    // Progression
    Route::get('/progress',                             [ProgressController::class, 'index']);
    Route::patch('/progress/{lesson}',                  [ProgressController::class, 'update']);

    // ── Espace Admin / Formateur ──────────────────────────────────
    Route::middleware('role:trainer,admin')
         ->prefix('admin')
         ->group(function () {
             Route::get('/students',              [Admin\StudentController::class, 'index']);
             Route::get('/students/{user}',       [Admin\StudentController::class, 'show']);
             Route::get('/dashboard',             [Admin\DashboardController::class, 'index']);
             Route::apiResource('/classes',       Admin\ClassController::class);
             Route::get('/reports',               Admin\ReportController::class);
         });
});
