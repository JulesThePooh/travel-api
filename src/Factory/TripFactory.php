<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Trip;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Trip>
 */
final class TripFactory extends PersistentObjectFactory
{
    private const array DESTINATIONS = [
        ['Lisbonne', 'Portugal'], ['Barcelone', 'Espagne'], ['Rome', 'Italie'], ['Amsterdam', 'Pays-Bas'],
        ['Berlin', 'Allemagne'], ['Prague', 'Tchéquie'], ['Budapest', 'Hongrie'], ['Marrakech', 'Maroc'],
        ['Annecy', 'France'], ['Biarritz', 'France'], ['Chamonix', 'France'], ['Marseille', 'France'],
        ['Copenhague', 'Danemark'], ['Édimbourg', 'Écosse'], ['Dubrovnik', 'Croatie'], ['Naples', 'Italie'],
        ['Séville', 'Espagne'], ['Porto', 'Portugal'], ['Athènes', 'Grèce'], ['Reykjavik', 'Islande'],
    ];

    private const array TITLES = [
        'Week-end entre potes à %s', 'Road trip %s', 'Escapade à %s', 'Vacances d\'été à %s',
        'EVG à %s', 'Séminaire détente à %s', 'Pont de mai à %s', 'Nouvel an à %s',
    ];

    #[\Override]
    public static function class(): string
    {
        return Trip::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        [$city, $country] = self::faker()->randomElement(self::DESTINATIONS);
        $start = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', '+8 months'))->setTime(0, 0);
        $end = $start->modify(\sprintf('+%d days', self::faker()->numberBetween(1, 12)));

        return [
            'title' => \sprintf(self::faker()->randomElement(self::TITLES), $city),
            'destination' => $city.', '.$country,
            'description' => self::faker()->optional(0.8)->paragraph(3),
            'startDate' => $start,
            'endDate' => $end,
            'budgetPerPerson' => self::faker()->optional(0.7)->numerify('###.00'),
            'owner' => UserFactory::new(),
            'createdAt' => $start->modify(\sprintf('-%d days', self::faker()->numberBetween(10, 90))),
        ];
    }

    public function upcoming(): self
    {
        $start = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 week', '+6 months'))->setTime(0, 0);

        return $this->with([
            'startDate' => $start,
            'endDate' => $start->modify(\sprintf('+%d days', self::faker()->numberBetween(2, 10))),
        ]);
    }

    public function past(): self
    {
        $start = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', '-1 month'))->setTime(0, 0);

        return $this->with([
            'startDate' => $start,
            'endDate' => $start->modify(\sprintf('+%d days', self::faker()->numberBetween(2, 10))),
        ]);
    }
}
