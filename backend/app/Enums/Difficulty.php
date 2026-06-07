<?php

namespace App\Enums;

enum Difficulty: string
{
    case BEGINNER = 'BEGINNER';
    case INTERMEDIATE = 'INTERMEDIATE';
    case ADVANCED = 'ADVANCED';

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => 'Debutant',
            self::INTERMEDIATE => 'Intermediaire',
            self::ADVANCED => 'Avance',
        };
    }
}
