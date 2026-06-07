<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\User;

class ExamIntegrityService
{
    public function createSession(User $user, string $quizId, string $module): ExamSession
    {
        ExamSession::query()
            ->where('user_id', $user->id)
            ->where('app_module', strtoupper($module))
            ->where('status', 'in_progress')
            ->update(['status' => 'abandoned', 'completed_at' => now()]);

        $session = ExamSession::query()->create([
            'user_id' => $user->id,
            'quiz_id' => $quizId,
            'app_module' => strtoupper($module),
            'status' => 'in_progress',
            'started_at' => now(),
            'integrity_logs' => [],
        ]);

        $session->appendLog('exam_session_created', [
            'userAgent' => request()->userAgent(),
            'ip' => request()->ip(),
        ]);

        return $session;
    }

    public function appendEvents(ExamSession $session, User $user, array $events): ExamSession
    {
        if ((int) $session->user_id !== (int) $user->id) {
            abort(403, 'Session examen invalide.');
        }

        if ($session->status !== 'in_progress') {
            return $session;
        }

        $logs = $session->integrity_logs ?? [];

        foreach ($events as $event) {
            $logs[] = [
                'type' => (string) ($event['type'] ?? 'unknown'),
                'at' => $event['at'] ?? now()->toISOString(),
                'meta' => is_array($event['meta'] ?? null) ? $event['meta'] : [],
            ];
        }

        $session->integrity_logs = $logs;
        $session->save();

        return $session->fresh();
    }

    public function completeSession(ExamSession $session, string $status = 'submitted'): void
    {
        $session->update([
            'status' => $status,
            'completed_at' => now(),
        ]);
    }
}
