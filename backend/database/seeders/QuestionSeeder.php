<?php

namespace Database\Seeders;

use App\Enums\Difficulty;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            // Priorite au dossier "fichier a utilise", puis fallback vers /content
            ['module' => 'WORD', 'path' => base_path('../fichier a utilise/word_quizzes.json'), 'fallback' => base_path('../content/word/quizzes.json')],
            ['module' => 'EXCEL', 'path' => base_path('../fichier a utilise/excel_quizzes.json'), 'fallback' => base_path('../content/excel/quizzes.json')],
            ['module' => 'POWERPOINT', 'path' => base_path('../fichier a utilise/powerpoint_quizzes.json'), 'fallback' => base_path('../content/powerpoint/quizzes.json')],
        ];

        foreach ($sources as $source) {
            $path = file_exists($source['path']) ? $source['path'] : $source['fallback'];
            if (! file_exists($path)) {
                continue;
            }

            $questions = json_decode((string) file_get_contents($path), true);
            if (! is_array($questions)) {
                continue;
            }

            foreach ($questions as $q) {
                $id = (string) ($q['id'] ?? '');
                if ($id === '') {
                    continue;
                }

                Question::updateOrCreate(
                    ['id' => $id],
                    [
                        'app_module' => $source['module'],
                        'domain' => (string) ($q['domain'] ?? 'general'),
                        'difficulty' => $this->mapDifficulty((int) ($q['difficulty'] ?? 1)),
                        'question_text' => (string) ($q['questionText'] ?? ''),
                        'options' => array_values((array) ($q['options'] ?? [])),
                        'correct_index' => (int) ($q['correctIndex'] ?? 0),
                        'explanation' => (string) ($q['explanation'] ?? ''),
                        'mos_objective' => $q['mosObjective'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function mapDifficulty(int $level): string
    {
        return match ($level) {
            1 => Difficulty::BEGINNER->value,
            2 => Difficulty::INTERMEDIATE->value,
            default => Difficulty::ADVANCED->value,
        };
    }
}
