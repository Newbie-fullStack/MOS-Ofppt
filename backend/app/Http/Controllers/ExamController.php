<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\SubmitExamRequest;
use App\Models\ExamSession;
use App\Models\Quiz;
use App\Services\BadgeAwardService;
use App\Services\ExamIntegrityService;
use App\Services\ExamScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamScoringService $scoringService,
        private readonly BadgeAwardService $badgeService,
        private readonly ExamIntegrityService $integrityService,
    ) {
    }

    /** Infos examen sans démarrer (pour la modale conditions). */
    public function preview(Request $request, string $module): JsonResponse
    {
        $quiz = $this->findExamQuiz($module);

        return response()->json([
            'data' => [
                'quizId' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'durationMin' => $quiz->duration_min,
                'totalQ' => $quiz->questions()->count(),
                'passingScore' => $quiz->passing_score,
                'module' => strtoupper($module),
                'conditions' => $this->examConditions(),
            ],
        ]);
    }

    /** Démarre une session examen + journal d'intégrité. */
    public function begin(Request $request, string $module): JsonResponse
    {
        $quiz = $this->findExamQuiz($module);
        $user = $request->user();

        $session = $this->integrityService->createSession($user, $quiz->id, $module);
        $session->appendLog('conditions_accepted', [
            'acceptedAt' => now()->toISOString(),
        ]);
        $session->appendLog('exam_started', []);

        $questions = $quiz->questions->shuffle()->map(fn ($question): array => [
            'id' => $question->id,
            'appModule' => $question->app_module?->value ?? $question->app_module,
            'domain' => $question->domain,
            'difficulty' => $question->difficulty?->value ?? $question->difficulty,
            'questionText' => $question->question_text,
            'options' => $question->options,
        ])->values();

        return response()->json([
            'data' => [
                'sessionId' => $session->id,
                'quizId' => $quiz->id,
                'title' => $quiz->title,
                'durationMin' => $quiz->duration_min,
                'totalQ' => $questions->count(),
                'passingScore' => $quiz->passing_score,
                'questions' => $questions,
                'startedAt' => $session->started_at->toISOString(),
            ],
            'message' => 'Session examen démarrée. Surveillance active.',
        ]);
    }

    public function logEvents(Request $request, ExamSession $session): JsonResponse
    {
        $validated = $request->validate([
            'events' => ['required', 'array', 'min:1', 'max:50'],
            'events.*.type' => ['required', 'string', 'max:80'],
            'events.*.at' => ['sometimes', 'string'],
            'events.*.meta' => ['sometimes', 'array'],
        ]);

        $updated = $this->integrityService->appendEvents(
            $session,
            $request->user(),
            $validated['events'],
        );

        return response()->json([
            'data' => [
                'sessionId' => $updated->id,
                'logCount' => count($updated->integrity_logs ?? []),
            ],
        ]);
    }

    public function submit(SubmitExamRequest $request, string $module): JsonResponse
    {
        $quiz = $this->findExamQuiz($module);
        $session = ExamSession::query()->findOrFail($request->validated('session_id'));

        if ((int) $session->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Session invalide.'], 403);
        }

        $session->appendLog('exam_submitted', [
            'durationSec' => (int) $request->validated('duration_sec'),
        ]);

        $result = $this->scoringService->scoreExamAttempt(
            quiz: $quiz,
            module: $module,
            answers: $request->validated('answers'),
            durationSec: (int) $request->validated('duration_sec'),
            startedAt: (string) $request->validated('started_at'),
            user: $request->user(),
            session: $session,
        );

        $this->integrityService->completeSession($session, 'submitted');
        $newBadges = $this->badgeService->checkAndAward($request->user());

        return response()->json([
            'data' => $result,
            'newBadges' => $newBadges,
            'message' => $result['passed']
                ? "Félicitations ! Examen blanc réussi avec {$result['score']}%."
                : "Score : {$result['score']}%. Continuez à pratiquer.",
        ]);
    }

    private function findExamQuiz(string $module): Quiz
    {
        return Quiz::query()
            ->where('app_module', strtoupper($module))
            ->where('is_exam_mode', true)
            ->where('is_published', true)
            ->with('questions')
            ->firstOrFail();
    }

    /** @return list<array{title: string, description: string}> */
    private function examConditions(): array
    {
        return [
            [
                'title' => 'Durée limitée',
                'description' => 'L\'examen doit être terminé dans le temps imparti. Le chronomètre démarre dès validation des conditions.',
            ],
            [
                'title' => 'Un seul onglet',
                'description' => 'Restez sur cet onglet. Changer d\'onglet, minimiser la fenêtre ou ouvrir une autre application est enregistré.',
            ],
            [
                'title' => 'Pas d\'aide externe',
                'description' => 'N\'utilisez pas de notes, messagerie, navigateur secondaire ni assistance tierce pendant l\'épreuve.',
            ],
            [
                'title' => 'Surveillance active',
                'description' => 'Vos actions (perte de focus, fermeture, copier-coller) sont journalisées pour le formateur OFPPT.',
            ],
            [
                'title' => 'Seuil de réussite',
                'description' => 'Un score minimum est requis pour valider l\'examen blanc MOS (affiché avant le démarrage).',
            ],
        ];
    }
}
