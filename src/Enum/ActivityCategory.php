<?php

declare(strict_types=1);

namespace App\Enum;

enum ActivityCategory: string
{
    case Visit = 'visit';
    case Food = 'food';
    case Sport = 'sport';
    case Nightlife = 'nightlife';
    case Transport = 'transport';
    case Relax = 'relax';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Visit => 'Visite',
            self::Food => 'Restaurant',
            self::Sport => 'Sport',
            self::Nightlife => 'Soirée',
            self::Transport => 'Transport',
            self::Relax => 'Détente',
            self::Other => 'Autre',
        };
    }
}
