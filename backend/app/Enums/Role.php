<?php

namespace App\Enums;

enum Role: string
{
    case STUDENT = 'STUDENT';
    case TRAINER = 'TRAINER';
    case ADMIN = 'ADMIN';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Apprenant',
            self::TRAINER => 'Formateur',
            self::ADMIN => 'Administrateur',
        };
    }
}
