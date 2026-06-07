<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Progress;
use App\Services\BadgeAwardService;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(
        private readonly XpService $xpService,
        private readonly BadgeAwardService $badgeService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $progress = Progress::query()
            ->where('user_id', $request->user()->id)
            ->with('lesson:id,slug,title,app_module,order,duration_min')
            ->get();

        return response()->json(['data' => $progress]);
    }

    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'completed' => ['sometimes', 'boolean'],
            'time_spent_sec' => ['sometimes', 'integer', 'min:0'],
        ]);

        $wasExisting = Progress::query()
            ->where('user_id', $request->user()->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        $progress = Progress::query()->updateOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            [
                'completed' => (bool) ($validated['completed'] ?? false),
                'completed_at' => ($validated['completed'] ?? false) ? now() : null,
                'time_spent_sec' => (int) ($validated['time_spent_sec'] ?? 0),
                'updated_at' => now(),
            ]
        );

        $newBadges = [];
        $xpAwarded = 0;
        if (! $wasExisting && ($validated['completed'] ?? false) === true) {
            $xpAwarded = $this->xpService->awardForLesson($request->user(), $lesson);
            $newBadges = $this->badgeService->checkAndAward($request->user());
        }

        return response()->json([
            'data' => $progress,
            'xpAwarded' => $xpAwarded,
            'newBadges' => $newBadges,
            'message' => 'Progression mise a jour.',
        ]);
    }
}
