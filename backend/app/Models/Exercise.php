<?php

namespace App\Models;

use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'instructions',
        'file_url',
        'solution_url',
        'difficulty',
        'order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'instructions' => 'array',
            'difficulty' => Difficulty::class,
            'is_published' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
