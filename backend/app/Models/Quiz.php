<?php

namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'app_module',
        'title',
        'description',
        'duration_min',
        'passing_score',
        'is_exam_mode',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'app_module' => AppModule::class,
            'duration_min' => 'integer',
            'passing_score' => 'integer',
            'is_exam_mode' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot('order')
            ->orderBy('quiz_questions.order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
