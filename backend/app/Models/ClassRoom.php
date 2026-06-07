<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ClassRoom extends Model
{
    use HasFactory;

    /** @var list<string> */
    public const ALLOWED_CODES = ['DD101', 'DD201', 'DD102', 'DD202'];

    protected $fillable = [
        'name',
        'description',
        'trainer_id',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ClassRoom $classRoom): void {
            if (! $classRoom->code) {
                $classRoom->code = strtoupper(Str::random(6));
            }
        });
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_members', 'class_room_id', 'user_id')
            ->withPivot('joined_at');
    }
}
