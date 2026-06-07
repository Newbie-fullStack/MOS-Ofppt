<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder
{
    public const CODES = ['DD101', 'DD201', 'DD102', 'DD202'];

    public function run(): void
    {
        $trainer = User::query()->where('email', 'formateur@mos-ofppt.ma')->first();
        if (! $trainer) {
            return;
        }

        $classes = [
            ['code' => 'DD101', 'name' => 'Développement Digital — Groupe 101', 'description' => 'Promotion DD — filière 101'],
            ['code' => 'DD201', 'name' => 'Développement Digital — Groupe 201', 'description' => 'Promotion DD — filière 201'],
            ['code' => 'DD102', 'name' => 'Développement Digital — Groupe 102', 'description' => 'Promotion DD — filière 102'],
            ['code' => 'DD202', 'name' => 'Développement Digital — Groupe 202', 'description' => 'Promotion DD — filière 202'],
        ];

        foreach ($classes as $row) {
            ClassRoom::updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'trainer_id' => $trainer->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
