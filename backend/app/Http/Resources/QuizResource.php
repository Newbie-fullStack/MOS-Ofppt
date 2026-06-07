<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appModule' => $this->app_module?->value ?? $this->app_module,
            'title' => $this->title,
            'description' => $this->description,
            'durationMin' => $this->duration_min,
            'passingScore' => $this->passing_score,
            'isExamMode' => $this->is_exam_mode,
            'isPublished' => $this->is_published,
            'questionsCount' => $this->whenCounted('questions'),
            'questions' => $this->whenLoaded('questions', function (): array {
                return $this->questions->map(function ($question): array {
                    return [
                        'id' => $question->id,
                        'appModule' => $question->app_module?->value ?? $question->app_module,
                        'domain' => $question->domain,
                        'difficulty' => $question->difficulty?->value ?? $question->difficulty,
                        'questionText' => $question->question_text,
                        'options' => $question->options,
                    ];
                })->values()->all();
            }),
        ];
    }
}
