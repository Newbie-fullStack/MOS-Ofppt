<?php

namespace App\Enums;

enum AppModule: string
{
    case WORD = 'WORD';
    case EXCEL = 'EXCEL';
    case POWERPOINT = 'POWERPOINT';

    public function label(): string
    {
        return match ($this) {
            self::WORD => 'Microsoft Word',
            self::EXCEL => 'Microsoft Excel',
            self::POWERPOINT => 'Microsoft PowerPoint',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::WORD => '#185FA5',
            self::EXCEL => '#1D6A47',
            self::POWERPOINT => '#C43E1C',
        };
    }
}
