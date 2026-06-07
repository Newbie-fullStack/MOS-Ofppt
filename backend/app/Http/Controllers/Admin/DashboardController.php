<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ExamAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtoupper((string) ($user->role->value ?? $user->role));

        $classQuery = ClassRoom::query();
        if ($role === 'TRAINER') {
            $classQuery->where('trainer_id', $user->id);
        }

        $classIds = $classQuery->pluck('id');
        $studentIds = User::query()
            ->where('role', 'STUDENT')
            ->when($role === 'TRAINER', function ($q) use ($classIds) {
                $q->whereHas('classes', fn ($c) => $c->whereIn('class_rooms.id', $classIds));
            })
            ->pluck('id');

        $quizAttempts = QuizAttempt::query()
            ->when($studentIds->isNotEmpty(), fn ($q) => $q->whereIn('user_id', $studentIds))
            ->count();

        $examAttempts = ExamAttempt::query()
            ->when($studentIds->isNotEmpty(), fn ($q) => $q->whereIn('user_id', $studentIds))
            ->count();

        $examPassed = ExamAttempt::query()
            ->when($studentIds->isNotEmpty(), fn ($q) => $q->whereIn('user_id', $studentIds))
            ->where('passed', true)
            ->count();

        $examPassRate = $examAttempts > 0
            ? (int) round(($examPassed / $examAttempts) * 100)
            : 0;

        $stats = [
            [
                'key' => 'students',
                'label' => 'Apprenants',
                'value' => $role === 'TRAINER'
                    ? $studentIds->count()
                    : User::query()->where('role', 'STUDENT')->count(),
            ],
            [
                'key' => 'trainers',
                'label' => 'Formateurs',
                'value' => User::query()->where('role', 'TRAINER')->count(),
            ],
            [
                'key' => 'classes',
                'label' => 'Classes',
                'value' => $classQuery->count(),
            ],
            [
                'key' => 'quizAttempts',
                'label' => 'Tentatives quiz',
                'value' => $quizAttempts,
            ],
            [
                'key' => 'examAttempts',
                'label' => 'Examens blancs',
                'value' => $examAttempts,
            ],
            [
                'key' => 'examPassRate',
                'label' => 'Taux réussite examens',
                'value' => $examPassRate,
                'suffix' => '%',
            ],
        ];

        $recentClasses = ClassRoom::query()
            ->when($role === 'TRAINER', fn ($q) => $q->where('trainer_id', $user->id))
            ->withCount('members')
            ->orderBy('code')
            ->get(['id', 'name', 'code'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'membersCount' => $c->members_count,
            ]);

        return response()->json([
            'data' => [
                'stats' => $stats,
                'recentClasses' => $recentClasses,
            ],
        ]);
    }
}
