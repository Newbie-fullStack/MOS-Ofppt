<?php

namespace Database\Seeders;

use App\Enums\Difficulty;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
    public function run(): void
    {
        $sources = [
            ['module' => 'WORD', 'path' => '/var/www/html/fichier a utilise/word_quizzes.json', 'fallback' => '/var/www/html/content/word/quizzes.json'],
            ['module' => 'EXCEL', 'path' => '/var/www/html/fichier a utilise/excel_quizzes.json', 'fallback' => '/var/www/html/content/excel/quizzes.json'],
            ['module' => 'POWERPOINT', 'path' => '/var/www/html/fichier a utilise/powerpoint_quizzes.json', 'fallback' => '/var/www/html/content/powerpoint/quizzes.json'],
        ];

        foreach ($sources as $source) {
            $path = file_exists($source['path']) ? $source['path'] : $source['fallback'];
            if (! file_exists($path)) {
                $this->command->warn("Fichier non trouvé : $path");
                continue;
            }

            $questions = json_decode((string) file_get_contents($path), true);
            if (! is_array($questions)) {
                continue;
            }

            foreach ($questions as $q) {
                $id = (string) ($q['id'] ?? '');
                if ($id === '') continue;

                \App\Models\Question::updateOrCreate(
                    ['id' => $id],
                    [
                        'app_module' => $source['module'],
                        'question_text' => $q['question_text'] ?? 'Pas de texte',
                        'options' => $q['options'] ?? [],
                        'answer' => $q['answer'] ?? '',
                        'difficulty' => 'BEGINNER',
                    ]
                );
            }
        }
    }
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
