<?php

namespace App\Models;

use App\Enums\AppModule;
use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'app_module',
        'title',
        'description',
        'order',
        'duration_min',
        'difficulty',
        'objectives',
        'mos_objectives',
        'content_json',
        'thumbnail_url',
        'video_url',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'app_module' => AppModule::class,
            'difficulty' => Difficulty::class,
            'objectives' => 'array',
            'mos_objectives' => 'array',
            'content_json' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('app_module', strtoupper($module));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}
