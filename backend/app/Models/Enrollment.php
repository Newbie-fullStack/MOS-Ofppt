<?php

namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'app_module',
        'enrolled_at',
        'target_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'app_module' => AppModule::class,
            'enrolled_at' => 'datetime',
            'target_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
