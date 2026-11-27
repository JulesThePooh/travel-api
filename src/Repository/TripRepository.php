<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\User;
use App\Enum\TripMemberStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /**
     * Voyages visibles par l'utilisateur : ceux qu'il organise + ceux où il est membre (non décliné).
     *
     * @return list<Trip>
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('o', 'm', 'mu')
            ->join('t.owner', 'o')
            ->leftJoin('t.members', 'm')
            ->leftJoin('m.user', 'mu')
            ->where('t.owner = :user')
            ->orWhere('t.id IN (
                SELECT IDENTITY(m2.trip) FROM App\Entity\TripMember m2
                WHERE m2.user = :user AND m2.status != :declined
            )')
            ->setParameter('user', $user)
            ->setParameter('declined', TripMemberStatus::Declined)
            ->orderBy('t.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
