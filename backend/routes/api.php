<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ClassMembershipController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::get('/user/stats', [UserController::class, 'stats']);
    Route::get('/user/badges', [BadgeController::class, 'index']);

    Route::get('/classes/available', [ClassMembershipController::class, 'available']);
    Route::post('/classes/join', [ClassMembershipController::class, 'join']);

    Route::get('/modules', [LessonController::class, 'modules']);
    Route::get('/modules/{module}/lessons', [LessonController::class, 'index']);
    Route::get('/modules/{module}/lessons/{slug}', [LessonController::class, 'show'])->name('api.v1.lessons.show');
    Route::post('/modules/{module}/enroll', [EnrollmentController::class, 'store']);

    Route::get('/modules/{module}/exercises', [ExerciseController::class, 'index']);
    Route::get('/exercises/{exercise}/download', [ExerciseController::class, 'download']);

    Route::get('/quizzes/{module}', [QuizController::class, 'index'])->whereIn('module', ['word', 'excel', 'powerpoint', 'WORD', 'EXCEL', 'POWERPOINT']);
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show']);
    Route::post('/quizzes/{quiz}/attempt', [QuizController::class, 'attempt']);

    Route::get('/exam/{module}', [ExamController::class, 'preview']);
    Route::post('/exam/{module}/begin', [ExamController::class, 'begin']);
    Route::post('/exam/session/{session}/events', [ExamController::class, 'logEvents']);
    Route::post('/exam/{module}/submit', [ExamController::class, 'submit']);

    Route::get('/progress', [ProgressController::class, 'index']);
    Route::patch('/progress/{lesson}', [ProgressController::class, 'update']);

    Route::middleware('role:trainer,admin')->prefix('admin')->group(function (): void {
        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{user}', [StudentController::class, 'show']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::apiResource('/classes', ClassController::class);
        Route::post('/classes/{class}/members', [ClassController::class, 'addMember']);
        Route::delete('/classes/{class}/members/{user}', [ClassController::class, 'removeMember']);
        Route::get('/reports', ReportController::class);
    });
});
