<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TripMemberStatus;
use App\Repository\TripMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Table de liaison Trip <-> User : qui participe à quel voyage, avec un statut.
 */
#[ORM\Entity(repositoryClass: TripMemberRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_TRIP_USER', fields: ['trip', 'user'])]
class TripMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Trip $trip = null;

    #[ORM\ManyToOne(inversedBy: 'tripMemberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 20, enumType: TripMemberStatus::class)]
    private TripMemberStatus $status = TripMemberStatus::Invited;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $joinedAt;

    public function __construct()
    {
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): TripMemberStatus
    {
        return $this->status;
    }

    public function setStatus(TripMemberStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getJoinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }
}
