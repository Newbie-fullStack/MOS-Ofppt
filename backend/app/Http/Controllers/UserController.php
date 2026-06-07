<?php

namespace App\Http\Controllers;

use App\Models\UserBadge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $badgesCount = UserBadge::query()->where('user_id', $user->id)->count();

        return response()->json([
            'data' => [
                'xpPoints' => $user->xp_points,
                'streakDays' => $user->streak_days,
                'badgesCount' => $badgesCount,
            ],
        ]);
    }
}
