<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'appModule' => $this->app_module?->value ?? $this->app_module,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'durationMin' => $this->duration_min,
            'difficulty' => $this->difficulty?->value ?? $this->difficulty,
            'objectives' => $this->objectives,
            'mosObjectives' => $this->mos_objectives,
            'thumbnailUrl' => $this->thumbnail_url,
            'isPublished' => $this->is_published,
            'content' => $this->when($request->routeIs('api.v1.lessons.show'), $this->content_json),
        ];
    }
}
