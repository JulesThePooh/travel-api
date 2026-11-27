<?php

declare(strict_types=1);

namespace App\Enum;

enum TripMemberStatus: string
{
    case Invited = 'invited';
    case Accepted = 'accepted';
    case Declined = 'declined';

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Invité',
            self::Accepted => 'Participe',
            self::Declined => 'Décliné',
        };
    }
}
