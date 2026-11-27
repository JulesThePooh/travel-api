<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    /** Hash bcrypt de "12345" (mis en dur pour éviter de hasher à chaque user). */
    public const string PASSWORD_HASH = '$2y$13$kDa/bSTmFRe7XFDVMwvNkePzX9BMyzbeKmDTsFQRnoXD8q8n/.TrO';

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        $firstName = self::faker()->firstName();
        $lastName = self::faker()->lastName();

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => self::faker()->unique()->safeEmail(),
            'password' => self::PASSWORD_HASH,
            'roles' => [],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2 years', '-1 month')),
        ];
    }
}
