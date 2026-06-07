<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Carbon;

class ExamScoringService
{
    public function scoreQuizAttempt(Quiz $quiz, array $answers, int $durationSec, User $user): QuizAttempt
    {
        $questions = $quiz->questions()->get()->keyBy('id');
        $correct = 0;
        $total = $questions->count();

        foreach ($answers as $questionId => $selectedIndex) {
            $question = $questions->get((string) $questionId);
            if ($question && (int) $selectedIndex === (int) $question->correct_index) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= (int) $quiz->passing_score;

        return QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_questions' => $total,
            'correct_questions' => $correct,
            'answers' => $answers,
            'duration_sec' => $durationSec,
            'passed' => $passed,
            'completed_at' => now(),
        ]);
    }

    public function scoreExamAttempt(
        Quiz $quiz,
        string $module,
        array $answers,
        int $durationSec,
        string $startedAt,
        User $user,
        ?ExamSession $session = null,
    ): array {
        $questions = $quiz->questions()->get()->keyBy('id');
        $correct = 0;
        $total = $questions->count();
        $details = [];

        foreach ($answers as $questionId => $selectedIndex) {
            $question = $questions->get((string) $questionId);
            $isCorrect = $question && (int) $selectedIndex === (int) $question->correct_index;
            if ($isCorrect) {
                $correct++;
            }

            $details[(string) $questionId] = [
                'selected' => (int) $selectedIndex,
                'correct_index' => $question?->correct_index,
                'is_correct' => $isCorrect,
                'explanation' => $question?->explanation,
            ];
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= (int) $quiz->passing_score;

        ExamAttempt::create([
            'user_id' => $user->id,
            'exam_session_id' => $session?->id,
            'app_module' => strtoupper($module),
            'score' => $score,
            'total_questions' => $total,
            'correct_questions' => $correct,
            'answers' => $answers,
            'integrity_logs' => $session?->integrity_logs ?? [],
            'passed' => $passed,
            'started_at' => Carbon::parse($startedAt),
            'completed_at' => now(),
            'duration_sec' => $durationSec,
        ]);

        return [
            'score' => $score,
            'total_questions' => $total,
            'correct_questions' => $correct,
            'passed' => $passed,
            'passing_score' => (int) $quiz->passing_score,
            'details' => $details,
        ];
    }
}
