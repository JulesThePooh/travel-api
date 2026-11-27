<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\TripMember;
use App\Enum\TripMemberStatus;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<TripMember>
 */
final class TripMemberFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return TripMember::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        return [
            'trip' => TripFactory::new(),
            'user' => UserFactory::new(),
            'status' => self::faker()->randomElement([
                TripMemberStatus::Accepted, TripMemberStatus::Accepted, TripMemberStatus::Accepted,
                TripMemberStatus::Invited, TripMemberStatus::Declined,
            ]),
            'joinedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months')),
        ];
    }

    public function accepted(): self
    {
        return $this->with(['status' => TripMemberStatus::Accepted]);
    }
}
