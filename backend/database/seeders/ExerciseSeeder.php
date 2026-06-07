<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        Lesson::query()->get()->each(function (Lesson $lesson): void {
            Exercise::updateOrCreate(
                ['lesson_id' => $lesson->id, 'order' => 1],
                [
                    'title' => 'Exercice pratique '.$lesson->title,
                    'description' => 'Mise en pratique des objectifs de la lecon.',
                    'instructions' => ['Ouvrir le fichier de travail', 'Appliquer les consignes', 'Sauvegarder le resultat'],
                    'file_url' => null,
                    'solution_url' => null,
                    'difficulty' => (string) ($lesson->difficulty->value ?? $lesson->difficulty),
                    'is_published' => true,
                ]
            );
        });
    }
}
