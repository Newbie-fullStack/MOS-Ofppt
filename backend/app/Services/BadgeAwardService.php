<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;

class BadgeAwardService
{
    public function checkAndAward(User $user): array
    {
        $newBadges = [];
        $badges = Badge::query()->get();

        foreach ($badges as $badge) {
            if ($user->hasBadge($badge->id)) {
                continue;
            }

            if (! $this->conditionMet($user, (string) $badge->condition)) {
                continue;
            }

            UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'earned_at' => now(),
            ]);

            $user->increment('xp_points', (int) $badge->xp_reward);
            $newBadges[] = [
                'id' => $badge->id,
                'name' => $badge->name,
                'xpReward' => (int) $badge->xp_reward,
            ];
        }

        return $newBadges;
    }

    private function conditionMet(User $user, string $condition): bool
    {
        return match ($condition) {
            'word_lesson_1' => $user->getCompletedLessonsForModule('WORD') >= 1,
            'excel_lesson_1' => $user->getCompletedLessonsForModule('EXCEL') >= 1,
            'ppt_lesson_1' => $user->getCompletedLessonsForModule('POWERPOINT') >= 1,
            'first_exam_attempt' => $user->examAttempts()->count() >= 1,
            default => false,
        };
    }
}
