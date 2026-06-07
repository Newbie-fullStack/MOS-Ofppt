<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $this->role?->value ?? $this->role;
        $class = $role === 'STUDENT'
            ? ($this->relationLoaded('classes') ? $this->classes->first() : $this->classes()->first())
            : null;

        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'role' => $role,
            'avatarUrl' => $this->avatar_url,
            'xpPoints' => $this->xp_points,
            'streakDays' => $this->streak_days,
            'isActive' => $this->is_active,
            'lastLoginAt' => $this->last_login_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'classRoom' => $class ? [
                'id' => $class->id,
                'name' => $class->name,
                'code' => $class->code,
            ] : null,
            'needsClassSelection' => $role === 'STUDENT' && ! $class,
        ];
    }
}
