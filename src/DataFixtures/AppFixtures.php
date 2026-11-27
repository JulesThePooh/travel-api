<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\TripMemberStatus;
use App\Factory\ActivityFactory;
use App\Factory\ExpenseFactory;
use App\Factory\TripFactory;
use App\Factory\TripMemberFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->faker->seed(1234);

        // --- Users en dur (mot de passe : 12345) ---
        $fixedUsers = [
            UserFactory::createOne(['email' => 'dev@drosalys.fr', 'firstName' => 'Dev', 'lastName' => 'Drosalys', 'roles' => ['ROLE_ADMIN']]),
            UserFactory::createOne(['email' => 'alice@travel.fr', 'firstName' => 'Alice', 'lastName' => 'Martin']),
            UserFactory::createOne(['email' => 'bob@travel.fr', 'firstName' => 'Bob', 'lastName' => 'Durand']),
            UserFactory::createOne(['email' => 'charlie@travel.fr', 'firstName' => 'Charlie', 'lastName' => 'Petit']),
        ];

        // --- Users aléatoires (mot de passe : 12345 aussi) ---
        $randomUsers = UserFactory::createMany(26);
        $allUsers = [...$fixedUsers, ...$randomUsers];

        // --- Voyages organisés par les users en dur : 2 à venir + 1 passé chacun ---
        foreach ($fixedUsers as $owner) {
            foreach ([TripFactory::new()->upcoming(), TripFactory::new()->upcoming(), TripFactory::new()->past()] as $factory) {
                $this->populateTrip($factory->create(['owner' => $owner]), $allUsers, $fixedUsers);
            }
        }

        // --- Voyages organisés par des users aléatoires ---
        for ($i = 0; $i < 15; ++$i) {
            $owner = $this->faker->randomElement($randomUsers);
            $this->populateTrip(TripFactory::createOne(['owner' => $owner]), $allUsers, $fixedUsers);
        }

        $manager->flush();
    }

    /**
     * Ajoute membres, activités et dépenses à un voyage.
     *
     * @param list<User> $allUsers
     * @param list<User> $fixedUsers
     */
    private function populateTrip(Trip $trip, array $allUsers, array $fixedUsers): void
    {
        $owner = $trip->getOwner();

        // L'owner participe forcément
        TripMemberFactory::createOne([
            'trip' => $trip,
            'user' => $owner,
            'status' => TripMemberStatus::Accepted,
            'joinedAt' => $trip->getCreatedAt(),
        ]);

        // Autres membres : quelques aléatoires + souvent un ou deux users en dur pour qu'ils aient du contenu
        $candidates = array_values(array_filter($allUsers, static fn (User $u): bool => $u !== $owner));
        $members = $this->faker->randomElements($candidates, $this->faker->numberBetween(2, 6));
        foreach ($this->faker->randomElements($fixedUsers, 2) as $fixed) {
            if ($fixed !== $owner && !\in_array($fixed, $members, true) && $this->faker->boolean(60)) {
                $members[] = $fixed;
            }
        }

        $accepted = [$owner];
        foreach ($members as $user) {
            $member = TripMemberFactory::createOne([
                'trip' => $trip,
                'user' => $user,
                'joinedAt' => $trip->getCreatedAt()->modify(\sprintf('+%d days', $this->faker->numberBetween(0, 9))),
            ]);
            if (TripMemberStatus::Accepted === $member->getStatus()) {
                $accepted[] = $user;
            }
        }

        // Activités réparties sur la durée du voyage
        $days = $trip->getDurationInDays();
        $activities = [];
        for ($i = 0, $n = $this->faker->numberBetween(2, 7); $i < $n; ++$i) {
            $start = $trip->getStartDate()
                ->modify(\sprintf('+%d days', $this->faker->numberBetween(0, max(0, $days - 1))))
                ->setTime($this->faker->numberBetween(8, 21), $this->faker->randomElement([0, 30]));

            $activity = ActivityFactory::createOne([
                'trip' => $trip,
                'startAt' => $start,
                'endAt' => $this->faker->boolean(70) ? $start->modify(\sprintf('+%d hours', $this->faker->numberBetween(1, 4))) : null,
                'createdBy' => $this->faker->randomElement($accepted),
            ]);

            $activities[] = $activity;

            foreach ($this->faker->randomElements($accepted, $this->faker->numberBetween(1, \count($accepted))) as $participant) {
                $activity->addParticipant($participant);
            }
        }

        // Dépenses (seulement si le voyage a commencé)
        if ($trip->getStartDate() <= new \DateTimeImmutable('today')) {
            for ($i = 0, $n = $this->faker->numberBetween(2, 8); $i < $n; ++$i) {
                $expense = ExpenseFactory::createOne([
                    'trip' => $trip,
                    'paidBy' => $this->faker->randomElement($accepted),
                    'paidAt' => $trip->getStartDate()->modify(\sprintf('+%d days', $this->faker->numberBetween(0, max(0, $days - 1)))),
                    'activity' => $this->faker->boolean(30) ? $this->faker->randomElement($activities) : null,
                ]);

                foreach ($this->faker->randomElements($accepted, $this->faker->numberBetween(min(2, \count($accepted)), \count($accepted))) as $user) {
                    $expense->addSharedWith($user);
                }
            }
        }
    }
}
