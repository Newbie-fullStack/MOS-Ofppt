<?php
// ══════════════════════════════════════════════════════════════════
// ENUMS PHP 8.1 — app/Enums/
// ══════════════════════════════════════════════════════════════════

// ── app/Enums/AppModule.php ───────────────────────────────────────
namespace App\Enums;

enum AppModule: string
{
    case WORD        = 'WORD';
    case EXCEL       = 'EXCEL';
    case POWERPOINT  = 'POWERPOINT';

    public function label(): string
    {
        return match($this) {
            self::WORD       => 'Microsoft Word',
            self::EXCEL      => 'Microsoft Excel',
            self::POWERPOINT => 'Microsoft PowerPoint',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::WORD       => '#185FA5',
            self::EXCEL      => '#1D6A47',
            self::POWERPOINT => '#C43E1C',
        };
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Enums/Role.php ────────────────────────────────────────────
namespace App\Enums;

enum Role: string
{
    case STUDENT = 'STUDENT';
    case TRAINER = 'TRAINER';
    case ADMIN   = 'ADMIN';

    public function label(): string
    {
        return match($this) {
            self::STUDENT => 'Apprenant',
            self::TRAINER => 'Formateur',
            self::ADMIN   => 'Administrateur',
        };
    }

    public function canAccessAdmin(): bool
    {
        return in_array($this, [self::TRAINER, self::ADMIN]);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ── app/Enums/Difficulty.php ──────────────────────────────────────
namespace App\Enums;

enum Difficulty: string
{
    case BEGINNER     = 'BEGINNER';
    case INTERMEDIATE = 'INTERMEDIATE';
    case ADVANCED     = 'ADVANCED';

    public function label(): string
    {
        return match($this) {
            self::BEGINNER     => 'Débutant',
            self::INTERMEDIATE => 'Intermédiaire',
            self::ADVANCED     => 'Avancé',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::BEGINNER     => '#16A34A',
            self::INTERMEDIATE => '#D97706',
            self::ADVANCED     => '#DC2626',
        };
    }

    public static function fromInt(int $level): self
    {
        return match($level) {
            1       => self::BEGINNER,
            2       => self::INTERMEDIATE,
            default => self::ADVANCED,
        };
    }
}
