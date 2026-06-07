<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['id' => 'badge-word-starter', 'name' => 'Word Starter', 'condition' => 'word_lesson_1'],
            ['id' => 'badge-excel-starter', 'name' => 'Excel Starter', 'condition' => 'excel_lesson_1'],
            ['id' => 'badge-ppt-starter', 'name' => 'PowerPoint Starter', 'condition' => 'ppt_lesson_1'],
            ['id' => 'badge-first-exam', 'name' => 'Premier Examen', 'condition' => 'first_exam_attempt'],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['id' => $badge['id']],
                [
                    'name' => $badge['name'],
                    'description' => 'Badge MOS OFPPT',
                    'icon_url' => 'badges/'.$badge['id'].'.svg',
                    'condition' => $badge['condition'],
                    'xp_reward' => 50,
                ]
            );
        }
    }
}
