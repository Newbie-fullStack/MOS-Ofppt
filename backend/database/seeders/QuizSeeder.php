<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['id' => 'quiz-word-full', 'app_module' => 'WORD', 'title' => 'Quiz Word Complet', 'is_exam_mode' => false],
            ['id' => 'quiz-excel-full', 'app_module' => 'EXCEL', 'title' => 'Quiz Excel Complet', 'is_exam_mode' => false],
            ['id' => 'quiz-powerpoint-full', 'app_module' => 'POWERPOINT', 'title' => 'Quiz PowerPoint Complet', 'is_exam_mode' => false],
            ['id' => 'exam-word-white', 'app_module' => 'WORD', 'title' => 'Examen Blanc Word', 'is_exam_mode' => true],
            ['id' => 'exam-excel-white', 'app_module' => 'EXCEL', 'title' => 'Examen Blanc Excel', 'is_exam_mode' => true],
            ['id' => 'exam-powerpoint-white', 'app_module' => 'POWERPOINT', 'title' => 'Examen Blanc PowerPoint', 'is_exam_mode' => true],
        ];

        foreach ($definitions as $def) {
            $quiz = Quiz::updateOrCreate(
                ['id' => $def['id']],
                [
                    'app_module' => $def['app_module'],
                    'title' => $def['title'],
                    'description' => 'Quiz genere automatiquement.',
                    'duration_min' => 15,
                    'passing_score' => 70,
                    'is_exam_mode' => $def['is_exam_mode'],
                    'is_published' => true,
                ]
            );

            $questionIds = Question::query()
                ->where('app_module', $def['app_module'])
                ->limit(20)
                ->pluck('id')
                ->values();

            $attach = [];
            foreach ($questionIds as $idx => $questionId) {
                $attach[$questionId] = ['order' => $idx + 1];
            }

            if ($attach !== []) {
                $quiz->questions()->sync($attach);
            }
        }
    }
}
