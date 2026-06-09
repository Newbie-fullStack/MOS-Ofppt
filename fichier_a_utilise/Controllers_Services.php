<?php
// ══════════════════════════════════════════════════════════════════
// CONTROLLERS, SERVICES, REQUESTS, RESOURCES — MOS OFPPT Laravel
// ══════════════════════════════════════════════════════════════════

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Http/Controllers/Auth/AuthController.php ─────────────────

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'role'       => 'STUDENT',
        ]);

        $token = $user->createToken('mos-token')->plainTextToken;

        return response()->json([
            'data'    => ['token' => $token, 'user' => new UserResource($user)],
            'message' => 'Compte créé avec succès.',
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects.',
                'error'   => 'INVALID_CREDENTIALS',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Compte désactivé. Contactez l\'OFPPT.',
                'error'   => 'ACCOUNT_DISABLED',
            ], 403);
        }

        // Révoquer les anciens tokens et en créer un nouveau
        $user->tokens()->delete();
        $token = $user->createToken('mos-token')->plainTextToken;

        $user->update(['last_login_at' => now()]);

        return response()->json([
            'data'    => ['token' => $token, 'user' => new UserResource($user)],
            'message' => 'Connexion réussie.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name'  => ['sometimes', 'string', 'max:100'],
            'avatar_url' => ['sometimes', 'url', 'nullable'],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'data'    => new UserResource($request->user()->fresh()),
            'message' => 'Profil mis à jour.',
        ]);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Http/Controllers/LessonController.php ────────────────────

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    // GET /modules — liste les 3 modules avec stats
    public function modules(Request $request): JsonResponse
    {
        $user    = $request->user();
        $modules = ['WORD', 'EXCEL', 'POWERPOINT'];

        $data = collect($modules)->map(function ($module) use ($user) {
            $totalLessons     = Lesson::published()->forModule($module)->count();
            $completedLessons = $user->getCompletedLessonsForModule($module);
            $isEnrolled       = $user->isEnrolledIn($module);
            $progressPct      = $totalLessons > 0
                ? round(($completedLessons / $totalLessons) * 100)
                : 0;

            return [
                'module'           => $module,
                'label'            => \App\Enums\AppModule::from($module)->label(),
                'color'            => \App\Enums\AppModule::from($module)->color(),
                'total_lessons'    => $totalLessons,
                'completed_lessons'=> $completedLessons,
                'progress_pct'     => $progressPct,
                'is_enrolled'      => $isEnrolled,
            ];
        });

        return response()->json(['data' => $data]);
    }

    // GET /modules/{module}/lessons
    public function index(string $module): JsonResponse
    {
        $lessons = Lesson::published()
                         ->forModule($module)
                         ->ordered()
                         ->get();

        return response()->json([
            'data' => LessonResource::collection($lessons),
        ]);
    }

    // GET /modules/{module}/lessons/{slug}
    public function show(string $module, string $slug): JsonResponse
    {
        $lesson = Lesson::published()
                        ->forModule($module)
                        ->where('slug', $slug)
                        ->firstOrFail();

        return response()->json([
            'data' => new LessonResource($lesson),
        ]);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Http/Controllers/QuizController.php ──────────────────────

namespace App\Http\Controllers;

use App\Http\Requests\Quiz\SubmitAttemptRequest;
use App\Http\Resources\QuizResource;
use App\Http\Resources\QuizAttemptResource;
use App\Models\Quiz;
use App\Services\ExamScoringService;
use App\Services\BadgeAwardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        private ExamScoringService $scoringService,
        private BadgeAwardService  $badgeService,
    ) {}

    // GET /quizzes/{module}
    public function index(string $module): JsonResponse
    {
        $quizzes = Quiz::published()
                       ->forModule($module)
                       ->examMode(false)
                       ->withCount('questions')
                       ->get();

        return response()->json([
            'data' => QuizResource::collection($quizzes),
        ]);
    }

    // GET /quizzes/{quiz}
    public function show(Quiz $quiz): JsonResponse
    {
        $quiz->load('questions');

        return response()->json([
            'data' => new QuizResource($quiz),
        ]);
    }

    // POST /quizzes/{quiz}/attempt
    public function attempt(SubmitAttemptRequest $request, Quiz $quiz): JsonResponse
    {
        $attempt = $this->scoringService->scoreQuizAttempt(
            quiz:        $quiz,
            answers:     $request->answers,
            durationSec: $request->duration_sec,
            user:        $request->user(),
        );

        // Vérifier et attribuer les badges éventuels
        $newBadges = $this->badgeService->checkAndAward($request->user());

        return response()->json([
            'data'       => new QuizAttemptResource($attempt),
            'new_badges' => $newBadges,
            'message'    => $attempt->passed
                ? 'Félicitations ! Quiz réussi.'
                : 'Quiz terminé. Continuez à pratiquer !',
        ]);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Http/Controllers/ExamController.php ──────────────────────

namespace App\Http\Controllers;

use App\Http\Requests\Exam\SubmitExamRequest;
use App\Http\Resources\QuizAttemptResource;
use App\Models\Quiz;
use App\Models\ExamAttempt;
use App\Services\ExamScoringService;
use App\Services\BadgeAwardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        private ExamScoringService $scoringService,
        private BadgeAwardService  $badgeService,
    ) {}

    // GET /exam/{module} — prépare l'examen blanc (retourne les questions sans réponses)
    public function start(Request $request, string $module): JsonResponse
    {
        $quiz = Quiz::forModule($module)
                    ->examMode(true)
                    ->published()
                    ->with('questions')
                    ->firstOrFail();

        // Mélanger les questions pour chaque tentative
        $questions = $quiz->questions->shuffle()->map(fn($q) => $q->toExamArray());

        return response()->json([
            'data' => [
                'quiz_id'     => $quiz->id,
                'title'       => $quiz->title,
                'duration_min'=> $quiz->duration_min,
                'total_q'     => $questions->count(),
                'passing_score'=> $quiz->passing_score,
                'questions'   => $questions,
                'started_at'  => now()->toISOString(),
            ],
        ]);
    }

    // POST /exam/{module}/submit
    public function submit(SubmitExamRequest $request, string $module): JsonResponse
    {
        $quiz = Quiz::forModule($module)->examMode(true)->published()->firstOrFail();

        $attempt = $this->scoringService->scoreExamAttempt(
            quiz:        $quiz,
            module:      $module,
            answers:     $request->answers,
            durationSec: $request->duration_sec,
            startedAt:   $request->started_at,
            user:        $request->user(),
        );

        $newBadges = $this->badgeService->checkAndAward($request->user());

        return response()->json([
            'data'       => $attempt,
            'new_badges' => $newBadges,
            'message'    => $attempt['passed']
                ? "🎉 Félicitations ! Vous avez réussi l'examen blanc avec {$attempt['score']}%."
                : "Score : {$attempt['score']}%. Score requis : {$quiz->passing_score}%. Continuez à pratiquer !",
        ]);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Http/Controllers/ProgressController.php ──────────────────

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Progress;
use App\Services\BadgeAwardService;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(
        private BadgeAwardService $badgeService,
        private XpService         $xpService,
    ) {}

    // GET /progress
    public function index(Request $request): JsonResponse
    {
        $progress = Progress::where('user_id', $request->user()->id)
                            ->with('lesson:id,slug,title,app_module,order,duration_min')
                            ->get();

        return response()->json(['data' => $progress]);
    }

    // PATCH /progress/{lesson}
    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'completed'      => ['boolean'],
            'time_spent_sec' => ['integer', 'min:0'],
        ]);

        $progress = Progress::updateOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            array_merge($validated, [
                'completed_at' => ($validated['completed'] ?? false) ? now() : null,
            ])
        );

        // XP si leçon complétée pour la première fois
        if ($progress->wasRecentlyCreated && ($validated['completed'] ?? false)) {
            $this->xpService->awardForLesson($request->user(), $lesson);
            $this->badgeService->checkAndAward($request->user());
        }

        return response()->json([
            'data'    => $progress,
            'message' => 'Progression mise à jour.',
        ]);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Services/ExamScoringService.php ──────────────────────────

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Support\Carbon;

class ExamScoringService
{
    // Scorer une tentative de quiz thématique
    public function scoreQuizAttempt(
        Quiz  $quiz,
        array $answers,
        int   $durationSec,
        User  $user,
    ): QuizAttempt {
        $questions = $quiz->questions()->get()->keyBy('id');
        $correct   = 0;
        $total     = $questions->count();

        foreach ($answers as $questionId => $selectedIndex) {
            $question = $questions->get($questionId);
            if ($question && (int)$selectedIndex === $question->correct_index) {
                $correct++;
            }
        }

        $score  = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        return QuizAttempt::create([
            'user_id'           => $user->id,
            'quiz_id'           => $quiz->id,
            'score'             => $score,
            'total_questions'   => $total,
            'correct_questions' => $correct,
            'answers'           => $answers,
            'duration_sec'      => $durationSec,
            'passed'            => $passed,
            'completed_at'      => now(),
        ]);
    }

    // Scorer un examen blanc
    public function scoreExamAttempt(
        Quiz   $quiz,
        string $module,
        array  $answers,
        int    $durationSec,
        string $startedAt,
        User   $user,
    ): array {
        $questions = $quiz->questions()->get()->keyBy('id');
        $correct   = 0;
        $total     = $questions->count();
        $details   = [];

        foreach ($answers as $questionId => $selectedIndex) {
            $question   = $questions->get($questionId);
            $isCorrect  = $question && (int)$selectedIndex === $question->correct_index;
            if ($isCorrect) $correct++;

            // Fournir la correction complète après l'examen
            $details[$questionId] = [
                'selected'      => (int)$selectedIndex,
                'correct_index' => $question?->correct_index,
                'is_correct'    => $isCorrect,
                'explanation'   => $question?->explanation,
            ];
        }

        $score  = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $quiz->passing_score;

        ExamAttempt::create([
            'user_id'           => $user->id,
            'app_module'        => strtoupper($module),
            'score'             => $score,
            'total_questions'   => $total,
            'correct_questions' => $correct,
            'answers'           => $answers,
            'passed'            => $passed,
            'started_at'        => Carbon::parse($startedAt),
            'completed_at'      => now(),
            'duration_sec'      => $durationSec,
        ]);

        return [
            'score'             => $score,
            'total_questions'   => $total,
            'correct_questions' => $correct,
            'passed'            => $passed,
            'passing_score'     => $quiz->passing_score,
            'details'           => $details,
        ];
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Services/BadgeAwardService.php ───────────────────────────

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;

class BadgeAwardService
{
    public function checkAndAward(User $user): array
    {
        $newBadges = [];
        $badges    = Badge::all();

        foreach ($badges as $badge) {
            if ($user->hasBadge($badge->id)) continue;

            if ($this->conditionMet($user, $badge->condition)) {
                UserBadge::create([
                    'user_id'   => $user->id,
                    'badge_id'  => $badge->id,
                    'earned_at' => now(),
                ]);

                $user->addXp($badge->xp_reward);
                $newBadges[] = ['id' => $badge->id, 'name' => $badge->name, 'xp_reward' => $badge->xp_reward];
            }
        }

        return $newBadges;
    }

    private function conditionMet(User $user, string $condition): bool
    {
        return match($condition) {
            'word_lesson_1'       => $user->getCompletedLessonsForModule('WORD') >= 1,
            'excel_lesson_1'      => $user->getCompletedLessonsForModule('EXCEL') >= 1,
            'ppt_lesson_1'        => $user->getCompletedLessonsForModule('POWERPOINT') >= 1,
            'exam_pass_word'      => $user->examAttempts()->where('app_module', 'WORD')->where('passed', true)->exists(),
            'exam_pass_excel'     => $user->examAttempts()->where('app_module', 'EXCEL')->where('passed', true)->exists(),
            'exam_pass_ppt'       => $user->examAttempts()->where('app_module', 'POWERPOINT')->where('passed', true)->exists(),
            'all_three_certified' => $user->examAttempts()->where('passed', true)->distinct('app_module')->count('app_module') >= 3,
            'streak_7'            => $user->streak_days >= 7,
            'streak_30'           => $user->streak_days >= 30,
            'first_exam_attempt'  => $user->examAttempts()->count() >= 1,
            'word_quiz_perfect'   => $user->quizAttempts()->where('score', 100)->whereHas('quiz', fn($q) => $q->where('app_module', 'WORD'))->exists(),
            default               => false,
        };
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Services/XpService.php ────────────────────────────────────

namespace App\Services;

use App\Models\Lesson;
use App\Models\User;
use App\Enums\Difficulty;

class XpService
{
    // XP selon la difficulté de la leçon
    private const XP_MAP = [
        'BEGINNER'     => 15,
        'INTERMEDIATE' => 25,
        'ADVANCED'     => 40,
    ];

    public function awardForLesson(User $user, Lesson $lesson): void
    {
        $xp = self::XP_MAP[$lesson->difficulty->value] ?? 15;
        $user->addXp($xp);
    }

    public function awardForQuiz(User $user, int $score): void
    {
        // XP proportionnel au score (max 50 XP pour 100%)
        $xp = (int) round(($score / 100) * 50);
        $user->addXp($xp);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── FORM REQUESTS ─────────────────────────────────────────────────

// app/Http/Requests/Auth/RegisterRequest.php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'      => ['required', 'email', 'unique:users,email', 'max:255'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'   => "L'adresse email est requise.",
            'email.unique'     => 'Cette adresse email est déjà utilisée.',
            'email.email'      => "Le format de l'email est invalide.",
            'password.min'     => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'=> 'Les mots de passe ne correspondent pas.',
            'first_name.required'=> 'Le prénom est requis.',
            'last_name.required' => 'Le nom est requis.',
        ];
    }
}

// app/Http/Requests/Auth/LoginRequest.php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}

// app/Http/Requests/Quiz/SubmitAttemptRequest.php
namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAttemptRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answers'      => ['required', 'array', 'min:1'],
            'answers.*'    => ['required', 'integer', 'min:0', 'max:3'],
            'duration_sec' => ['required', 'integer', 'min:1', 'max:10800'],
        ];
    }
}

// app/Http/Requests/Exam/SubmitExamRequest.php
namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answers'      => ['required', 'array', 'min:1'],
            'answers.*'    => ['required', 'integer', 'min:0', 'max:3'],
            'duration_sec' => ['required', 'integer', 'min:1'],
            'started_at'   => ['required', 'date'],
        ];
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── API RESOURCES ─────────────────────────────────────────────────

// app/Http/Resources/UserResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'firstName'  => $this->first_name,
            'lastName'   => $this->last_name,
            'fullName'   => $this->full_name,
            'role'       => $this->role->value,
            'avatarUrl'  => $this->avatar_url,
            'xpPoints'   => $this->xp_points,
            'streakDays' => $this->streak_days,
            'createdAt'  => $this->created_at?->toISOString(),
        ];
    }
}

// app/Http/Resources/LessonResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'appModule'     => $this->app_module->value,
            'title'         => $this->title,
            'description'   => $this->description,
            'order'         => $this->order,
            'durationMin'   => $this->duration_min,
            'difficulty'    => $this->difficulty->value,
            'difficultyLabel'=> $this->difficulty->label(),
            'objectives'    => $this->objectives,
            'mosObjectives' => $this->mos_objectives,
            'thumbnailUrl'  => $this->thumbnail_url,
            'isPublished'   => $this->is_published,
            // contentJson uniquement sur le détail (pas dans la liste)
            'content'       => $this->when(
                $request->routeIs('*.lessons.show'),
                $this->content_json
            ),
        ];
    }
}

// app/Http/Resources/QuizAttemptResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'score'            => $this->score,
            'totalQuestions'   => $this->total_questions,
            'correctQuestions' => $this->correct_questions,
            'passed'           => $this->passed,
            'durationSec'      => $this->duration_sec,
            'completedAt'      => $this->completed_at?->toISOString(),
            'quiz'             => [
                'id'           => $this->quiz_id,
                'title'        => $this->quiz?->title,
                'passingScore' => $this->quiz?->passing_score,
            ],
        ];
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Http/Middleware/EnsureRole.php ────────────────────────────

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if (!in_array($user->role->value, array_map('strtoupper', $roles))) {
            return response()->json([
                'message' => 'Accès refusé. Droits insuffisants.',
                'error'   => 'INSUFFICIENT_PERMISSIONS',
            ], 403);
        }

        return $next($request);
    }
}
