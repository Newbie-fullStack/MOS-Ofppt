<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\User;

class XpService
{
    private const XP_MAP = [
        'BEGINNER' => 15,
        'INTERMEDIATE' => 25,
        'ADVANCED' => 40,
    ];

    public function awardForLesson(User $user, Lesson $lesson): int
    {
        $difficulty = (string) ($lesson->difficulty->value ?? $lesson->difficulty);
        $xp = self::XP_MAP[$difficulty] ?? 15;
        $user->increment('xp_points', $xp);

        return $xp;
    }
}
