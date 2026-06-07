<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ExamAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ReportController extends Controller
{
    private const MODULE_LABELS = [
        'WORD' => 'Microsoft Word',
        'EXCEL' => 'Microsoft Excel',
        'POWERPOINT' => 'Microsoft PowerPoint',
    ];

    public function __invoke(Request $request): JsonResponse
    {
        $classId = $request->query('class_room_id');
        $studentIds = null;

        if ($classId) {
            $class = ClassRoom::query()->findOrFail($classId);
            $studentIds = $class->members()->pluck('users.id');
        }

        $topQuizUsers = QuizAttempt::query()
            ->when($studentIds !== null, fn ($q) => $q->whereIn('user_id', $studentIds))
            ->select('user_id')
            ->selectRaw('ROUND(AVG(score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts')
            ->selectRaw('MAX(score) as best_score')
            ->groupBy('user_id')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $user = User::query()->find($row->user_id);
                $class = $user?->classes()->first();

                return [
                    'userId' => $row->user_id,
                    'fullName' => $user ? trim($user->first_name.' '.$user->last_name) : '—',
                    'email' => $user?->email,
                    'classCode' => $class?->code,
                    'avgScore' => (float) $row->avg_score,
                    'bestScore' => (int) $row->best_score,
                    'attempts' => (int) $row->attempts,
                ];
            });

        $moduleExamStats = ExamAttempt::query()
            ->when($studentIds !== null, fn ($q) => $q->whereIn('user_id', $studentIds))
            ->select('app_module')
            ->selectRaw('COUNT(*) as attempts')
            ->selectRaw('ROUND(AVG(score), 1) as avg_score')
            ->selectRaw('SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_count')
            ->groupBy('app_module')
            ->orderBy('app_module')
            ->get()
            ->map(function ($row) {
                $moduleValue = $row->app_module?->value ?? $row->app_module;
                return [
                    'module' => $moduleValue,
                    'moduleLabel' => self::MODULE_LABELS[strtoupper((string) $moduleValue)] ?? $moduleValue,
                    'attempts' => (int) $row->attempts,
                    'avgScore' => (float) $row->avg_score,
                    'passedCount' => (int) $row->passed_count,
                    'passRate' => $row->attempts > 0
                        ? (int) round(((int) $row->passed_count / (int) $row->attempts) * 100)
                        : 0,
                ];
            });

        $classOverview = ClassRoom::query()
            ->with(['trainer:id,first_name,last_name'])
            ->withCount('members')
            ->orderBy('code')
            ->get()
            ->map(function (ClassRoom $class) {
                $memberIds = $class->members()->pluck('users.id');
                $quizAttempts = QuizAttempt::query()->whereIn('user_id', $memberIds)->count();
                $examAttempts = ExamAttempt::query()->whereIn('user_id', $memberIds)->count();
                $avgQuiz = QuizAttempt::query()
                    ->whereIn('user_id', $memberIds)
                    ->avg('score');

                return [
                    'id' => $class->id,
                    'code' => $class->code,
                    'name' => $class->name,
                    'trainerName' => $class->trainer
                        ? trim($class->trainer->first_name.' '.$class->trainer->last_name)
                        : '—',
                    'membersCount' => $class->members_count,
                    'quizAttempts' => $quizAttempts,
                    'examAttempts' => $examAttempts,
                    'avgQuizScore' => $avgQuiz ? round((float) $avgQuiz, 1) : null,
                ];
            });

        $studentsWithoutClass = User::query()
            ->where('role', 'STUDENT')
            ->whereDoesntHave('classes')
            ->count();

        $recentExamSubmissions = ExamAttempt::query()
            ->when($studentIds !== null, fn ($q) => $q->whereIn('user_id', $studentIds))
            ->with(['user.classes'])
            ->orderByDesc('completed_at')
            ->limit(20)
            ->get()
            ->map(function (ExamAttempt $attempt) {
                $user = $attempt->user;
                $class = $user?->classes()->first();

                $logs = $attempt->integrity_logs ?? [];
                $violationCount = 0;
                foreach ($logs as $log) {
                    if (in_array($log['type'] ?? '', ['tab_switch', 'window_blur', 'copy_paste', 'context_menu'], true)) {
                        $violationCount++;
                    }
                }

                $moduleValue = $attempt->app_module?->value ?? $attempt->app_module;

                return [
                    'id' => $attempt->id,
                    'userId' => $attempt->user_id,
                    'fullName' => $user ? trim($user->first_name.' '.$user->last_name) : '—',
                    'classCode' => $class?->code,
                    'module' => $moduleValue,
                    'moduleLabel' => self::MODULE_LABELS[strtoupper((string) $moduleValue)] ?? $moduleValue,
                    'score' => $attempt->score,
                    'passed' => $attempt->passed,
                    'totalQuestions' => $attempt->total_questions,
                    'correctQuestions' => $attempt->correct_questions,
                    'durationSec' => $attempt->duration_sec,
                    'completedAt' => $attempt->completed_at?->toISOString(),
                    'sessionId' => $attempt->exam_session_id,
                    'violationCount' => $violationCount,
                    'integrityLogs' => $logs,
                ];
            });

        return response()->json([
            'data' => [
                'topQuizUsers' => $topQuizUsers,
                'moduleExamStats' => $moduleExamStats,
                'classOverview' => $classOverview,
                'studentsWithoutClass' => $studentsWithoutClass,
                'recentExamSubmissions' => $recentExamSubmissions,
                'filterClassId' => $classId ? (int) $classId : null,
            ],
        ]);
    }
}
