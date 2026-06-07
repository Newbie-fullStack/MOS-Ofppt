<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'totalQuestions' => $this->total_questions,
            'correctQuestions' => $this->correct_questions,
            'passed' => $this->passed,
            'durationSec' => $this->duration_sec,
            'completedAt' => $this->completed_at?->toISOString(),
            'quiz' => [
                'id' => $this->quiz_id,
                'title' => $this->quiz?->title,
                'passingScore' => $this->quiz?->passing_score,
            ],
        ];
    }
}
