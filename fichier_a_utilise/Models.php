<?php
// ══════════════════════════════════════════════════════════════════
// MODÈLES ELOQUENT — app/Models/
// ══════════════════════════════════════════════════════════════════

// ── app/Models/User.php ───────────────────────────────────────────
namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email', 'password', 'first_name', 'last_name',
        'role', 'avatar_url', 'xp_points', 'streak_days',
        'last_login_at', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'role'          => Role::class,
        'xp_points'     => 'integer',
        'streak_days'   => 'integer',
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_members', 'user_id', 'class_room_id')
                    ->withPivot('joined_at');
    }

    // ── Helpers ───────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isEnrolledIn(string $module): bool
    {
        return $this->enrollments()->where('app_module', $module)->exists();
    }

    public function hasBadge(string $badgeId): bool
    {
        return $this->userBadges()->where('badge_id', $badgeId)->exists();
    }

    public function addXp(int $points): void
    {
        $this->increment('xp_points', $points);
    }

    public function getCompletedLessonsForModule(string $module): int
    {
        return $this->progress()
                    ->whereHas('lesson', fn($q) => $q->where('app_module', $module))
                    ->where('completed', true)
                    ->count();
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/Lesson.php ─────────────────────────────────────────
namespace App\Models;

use App\Enums\AppModule;
use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'app_module', 'title', 'description', 'order',
        'duration_min', 'difficulty', 'objectives', 'mos_objectives',
        'content_json', 'thumbnail_url', 'video_url', 'is_published',
    ];

    protected $casts = [
        'app_module'     => AppModule::class,
        'difficulty'     => Difficulty::class,
        'objectives'     => 'array',
        'mos_objectives' => 'array',
        'content_json'   => 'array',
        'is_published'   => 'boolean',
        'order'          => 'integer',
        'duration_min'   => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('app_module', strtoupper($module));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // ── Relations ─────────────────────────────────────────────────
    public function exercises()
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    // ── Helper ────────────────────────────────────────────────────
    public function isCompletedByUser(int $userId): bool
    {
        return $this->progress()
                    ->where('user_id', $userId)
                    ->where('completed', true)
                    ->exists();
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/Question.php ───────────────────────────────────────
namespace App\Models;

use App\Enums\AppModule;
use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'app_module', 'domain', 'difficulty',
        'question_text', 'options', 'correct_index',
        'explanation', 'mos_objective', 'image_url', 'is_active',
    ];

    protected $casts = [
        'app_module'    => AppModule::class,
        'difficulty'    => Difficulty::class,
        'options'       => 'array',
        'correct_index' => 'integer',
        'is_active'     => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────
    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions')
                    ->withPivot('order');
    }

    // ── Scope ─────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('app_module', strtoupper($module));
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    // ── Helper : masquer la réponse correcte pour l'examen ────────
    public function toExamArray(): array
    {
        return [
            'id'           => $this->id,
            'app_module'   => $this->app_module,
            'domain'       => $this->domain,
            'difficulty'   => $this->difficulty,
            'question_text'=> $this->question_text,
            'options'      => $this->options,
            // correct_index et explanation NON inclus pendant l'examen
        ];
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/Quiz.php ───────────────────────────────────────────
namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'app_module', 'title', 'description',
        'duration_min', 'passing_score', 'is_exam_mode', 'is_published',
    ];

    protected $casts = [
        'app_module'    => AppModule::class,
        'duration_min'  => 'integer',
        'passing_score' => 'integer',
        'is_exam_mode'  => 'boolean',
        'is_published'  => 'boolean',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
                    ->withPivot('order')
                    ->orderBy('quiz_questions.order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('app_module', strtoupper($module));
    }

    public function scopeExamMode($query, bool $isExam = true)
    {
        return $query->where('is_exam_mode', $isExam);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/QuizAttempt.php ────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id', 'quiz_id', 'score', 'total_questions',
        'correct_questions', 'answers', 'duration_sec', 'passed', 'completed_at',
    ];

    protected $casts = [
        'score'             => 'integer',
        'total_questions'   => 'integer',
        'correct_questions' => 'integer',
        'answers'           => 'array',
        'duration_sec'      => 'integer',
        'passed'            => 'boolean',
        'completed_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/ExamAttempt.php ────────────────────────────────────
namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'user_id', 'app_module', 'score', 'total_questions',
        'correct_questions', 'answers', 'passed',
        'started_at', 'completed_at', 'duration_sec',
    ];

    protected $casts = [
        'app_module'        => AppModule::class,
        'score'             => 'integer',
        'total_questions'   => 'integer',
        'correct_questions' => 'integer',
        'answers'           => 'array',
        'passed'            => 'boolean',
        'started_at'        => 'datetime',
        'completed_at'      => 'datetime',
        'duration_sec'      => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/Progress.php ───────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = null; // Clé composite

    protected $fillable = [
        'user_id', 'lesson_id', 'completed',
        'completed_at', 'time_spent_sec',
    ];

    protected $casts = [
        'completed'      => 'boolean',
        'completed_at'   => 'datetime',
        'time_spent_sec' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/Badge.php ──────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'name', 'description', 'icon_url', 'condition', 'xp_reward',
    ];

    protected $casts = [
        'xp_reward' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at');
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/UserBadge.php ──────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    public $timestamps   = false;
    public $incrementing = false;

    protected $fillable = ['user_id', 'badge_id', 'earned_at'];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/Enrollment.php ─────────────────────────────────────
namespace App\Models;

use App\Enums\AppModule;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    public $incrementing = false;
    public $timestamps   = false;

    protected $fillable = [
        'user_id', 'app_module', 'enrolled_at', 'target_date', 'completed_at',
    ];

    protected $casts = [
        'app_module'   => AppModule::class,
        'enrolled_at'  => 'datetime',
        'target_date'  => 'date',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Models/ClassRoom.php ──────────────────────────────────────
// Note : "Class" est un mot réservé PHP → ClassRoom
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'trainer_id', 'code', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Générer automatiquement un code d'invitation unique
    protected static function booted(): void
    {
        static::creating(function (ClassRoom $classRoom) {
            if (empty($classRoom->code)) {
                $classRoom->code = strtoupper(Str::random(6));
            }
        });
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'class_members', 'class_room_id', 'user_id')
                    ->withPivot('joined_at');
    }
}
