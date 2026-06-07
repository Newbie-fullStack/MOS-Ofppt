<?php

namespace App\Http\Controllers;

use App\Models\UserBadge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $badges = UserBadge::query()
            ->where('user_id', $request->user()->id)
            ->with('badge')
            ->orderByDesc('earned_at')
            ->get()
            ->map(fn (UserBadge $userBadge): array => [
                'id' => $userBadge->badge?->id,
                'name' => $userBadge->badge?->name,
                'description' => $userBadge->badge?->description,
                'iconUrl' => $userBadge->badge?->icon_url,
                'xpReward' => $userBadge->badge?->xp_reward,
                'earnedAt' => $userBadge->earned_at?->toISOString(),
            ])
            ->values();

        return response()->json([
            'data' => $badges,
        ]);
    }
}
