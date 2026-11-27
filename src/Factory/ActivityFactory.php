<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Activity;
use App\Enum\ActivityCategory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Activity>
 */
final class ActivityFactory extends PersistentObjectFactory
{
    private const array NAMES = [
        ActivityCategory::Visit->value => ['Visite guidée du centre historique', 'Musée d\'art moderne', 'Free walking tour', 'Visite du château', 'Balade en bateau'],
        ActivityCategory::Food->value => ['Dîner dans un resto local', 'Cours de cuisine', 'Brunch du dimanche', 'Dégustation de vins', 'Food tour'],
        ActivityCategory::Sport->value => ['Kayak', 'Randonnée', 'Session de surf', 'Location de vélos', 'Escalade'],
        ActivityCategory::Nightlife->value => ['Bar à cocktails', 'Concert', 'Soirée en club', 'Karaoké'],
        ActivityCategory::Transport->value => ['Location de voiture', 'Billets de train', 'Navette aéroport', 'Ferry'],
        ActivityCategory::Relax->value => ['Spa & hammam', 'Après-midi plage', 'Pique-nique au parc', 'Yoga au lever du soleil'],
        ActivityCategory::Other->value => ['Escape game', 'Shopping au marché', 'Atelier poterie'],
    ];

    #[\Override]
    public static function class(): string
    {
        return Activity::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        $category = self::faker()->randomElement(ActivityCategory::cases());
        $start = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('now', '+1 month'))
            ->setTime(self::faker()->numberBetween(8, 21), self::faker()->randomElement([0, 30]));

        return [
            'trip' => TripFactory::new(),
            'name' => self::faker()->randomElement(self::NAMES[$category->value]),
            'description' => self::faker()->optional(0.6)->sentence(12),
            'category' => $category,
            'location' => self::faker()->optional(0.8)->streetAddress(),
            'startAt' => $start,
            'endAt' => self::faker()->boolean(70) ? $start->modify(\sprintf('+%d hours', self::faker()->numberBetween(1, 4))) : null,
            'pricePerPerson' => self::faker()->boolean(15) ? '0.00' : number_format(self::faker()->randomFloat(2, 5, 150), 2, '.', ''),
            'maxParticipants' => self::faker()->optional(0.3)->numberBetween(2, 12),
            'bookingUrl' => self::faker()->optional(0.4)->url(),
        ];
    }
}
