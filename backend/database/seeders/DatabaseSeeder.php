<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LessonSeeder::class,
            ExerciseSeeder::class,
            QuestionSeeder::class,
            QuizSeeder::class,
            BadgeSeeder::class,
            ClassRoomSeeder::class,
        ]);

        Artisan::call('lessons:seed-content');
    }
}
