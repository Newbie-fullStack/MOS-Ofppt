<?php

namespace Database\Seeders;

use App\Enums\Difficulty;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            ['id' => 'q1', 'module' => 'WORD', 'text' => 'Quel raccourci permet d\'appliquer le style Titre 1 ?', 'options' => ['Ctrl+Alt+1', 'Ctrl+1', 'Alt+1'], 'answer' => 'Ctrl+Alt+1'],
            ['id' => 'q2', 'module' => 'EXCEL', 'text' => 'Quelle fonction permet de calculer la somme ?', 'options' => ['=SOMME()', '=TOTAL()', '=PLUS()'], 'answer' => '=SOMME()'],
            ['id' => 'q3', 'module' => 'POWERPOINT', 'text' => 'Quel raccourci pour lancer le diaporama ?', 'options' => ['F5', 'F1', 'F12'], 'answer' => 'F5'],
        ];

        foreach ($questions as $q) {
            \App\Models\Question::updateOrCreate(
                ['id' => $q['id']],
                [
                    'app_module' => $q['module'],
                    'question_text' => $q['text'],
                    'options' => $q['options'],
                    'answer' => $q['answer'],
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
