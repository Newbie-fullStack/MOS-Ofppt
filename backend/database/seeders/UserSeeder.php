<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['email' => 'apprenant@mos-ofppt.ma', 'first_name' => 'Compte', 'last_name' => 'Apprenant', 'role' => Role::STUDENT->value],
            ['email' => 'formateur@mos-ofppt.ma', 'first_name' => 'Compte', 'last_name' => 'Formateur', 'role' => Role::TRAINER->value],
            ['email' => 'admin@mos-ofppt.ma', 'first_name' => 'Compte', 'last_name' => 'Admin', 'role' => Role::ADMIN->value],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('Test1234!'),
                    'is_active' => true,
                ])
            );
        }
    }
}
