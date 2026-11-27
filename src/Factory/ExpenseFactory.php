<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Expense;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Expense>
 */
final class ExpenseFactory extends PersistentObjectFactory
{
    private const array LABELS = [
        'Courses', 'Essence', 'Péage', 'Logement Airbnb', 'Resto du soir', 'Apéro', 'Parking',
        'Billets de musée', 'Taxi', 'Petit-déjeuner', 'Boulangerie', 'Glaces',
    ];

    #[\Override]
    public static function class(): string
    {
        return Expense::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        return [
            'trip' => TripFactory::new(),
            'label' => self::faker()->randomElement(self::LABELS),
            'amount' => number_format(self::faker()->randomFloat(2, 4, 600), 2, '.', ''),
            'paidAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month')),
            'paidBy' => UserFactory::new(),
        ];
    }
}
