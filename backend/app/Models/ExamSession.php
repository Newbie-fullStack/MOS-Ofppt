<?php

namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSession extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'app_module',
        'status',
        'started_at',
        'completed_at',
        'integrity_logs',
    ];

    protected function casts(): array
    {
        return [
            'app_module' => AppModule::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'integrity_logs' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function appendLog(string $type, array $meta = []): void
    {
        $logs = $this->integrity_logs ?? [];
        $logs[] = [
            'type' => $type,
            'at' => now()->toISOString(),
            'meta' => $meta,
        ];
        $this->integrity_logs = $logs;
        $this->save();
    }
}
