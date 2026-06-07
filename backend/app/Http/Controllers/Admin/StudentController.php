<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $classRoomId = $request->query('class_room_id');
        $unassigned = $request->boolean('unassigned');

        $students = User::query()
            ->where('role', 'STUDENT')
            ->with(['classes:id,name,code'])
            ->when($unassigned, fn ($q) => $q->whereDoesntHave('classes'))
            ->when($classRoomId && ! $unassigned, function ($q) use ($classRoomId) {
                $q->whereHas('classes', fn ($c) => $c->where('class_rooms.id', $classRoomId));
            })
            ->select('id', 'email', 'first_name', 'last_name', 'xp_points', 'streak_days', 'is_active')
            ->orderByDesc('xp_points')
            ->paginate(20);

        $items = collect($students->items())->map(function (User $user) {
            $class = $user->classes->first();

            return [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'xp_points' => $user->xp_points,
                'streak_days' => $user->streak_days,
                'is_active' => $user->is_active,
                'class_code' => $class?->code,
                'class_name' => $class?->name,
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $class = $user->classes()->first();

        return response()->json([
            'data' => [
                'user' => $user->loadCount(['quizAttempts', 'examAttempts', 'userBadges']),
                'classRoom' => $class ? [
                    'id' => $class->id,
                    'name' => $class->name,
                    'code' => $class->code,
                ] : null,
            ],
        ]);
    }
}
