<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quiz\SubmitAttemptRequest;
use App\Http\Resources\QuizAttemptResource;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Services\BadgeAwardService;
use App\Services\ExamScoringService;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    public function __construct(
        private readonly ExamScoringService $scoringService,
        private readonly BadgeAwardService $badgeService,
    ) {
    }

    public function index(string $module): JsonResponse
    {
        $quizzes = Quiz::query()
            ->where('app_module', strtoupper($module))
            ->where('is_exam_mode', false)
            ->where('is_published', true)
            ->withCount('questions')
            ->get();

        return response()->json([
            'data' => QuizResource::collection($quizzes),
        ]);
    }

    public function show(Quiz $quiz): JsonResponse
    {
        $quiz->load('questions');

        return response()->json([
            'data' => new QuizResource($quiz),
        ]);
    }

    public function attempt(SubmitAttemptRequest $request, Quiz $quiz): JsonResponse
    {
        $attempt = $this->scoringService->scoreQuizAttempt(
            quiz: $quiz,
            answers: $request->validated('answers'),
            durationSec: (int) $request->validated('duration_sec'),
            user: $request->user(),
        );
        $newBadges = $this->badgeService->checkAndAward($request->user());

        return response()->json([
            'data' => new QuizAttemptResource($attempt->load('quiz')),
            'newBadges' => $newBadges,
            'message' => $attempt->passed ? 'Felicitations ! Quiz reussi.' : 'Quiz termine. Continuez a pratiquer.',
        ]);
    }
}
