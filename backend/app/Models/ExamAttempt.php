<?php

namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'exam_session_id',
        'app_module',
        'score',
        'total_questions',
        'correct_questions',
        'answers',
        'integrity_logs',
        'passed',
        'started_at',
        'completed_at',
        'duration_sec',
    ];

    protected function casts(): array
    {
        return [
            'app_module' => AppModule::class,
            'answers' => 'array',
            'integrity_logs' => 'array',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
