<?php

namespace App\Models;

use App\Enums\AppModule;
use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'app_module',
        'domain',
        'difficulty',
        'question_text',
        'options',
        'correct_index',
        'explanation',
        'mos_objective',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'app_module' => AppModule::class,
            'difficulty' => Difficulty::class,
            'options' => 'array',
            'correct_index' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions')
            ->withPivot('order');
    }
}
