<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\TripMemberStatus;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $destination = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startDate', message: 'La date de fin doit être après la date de début.')]
    private ?\DateTimeImmutable $endDate = null;

    /** Budget prévisionnel par personne (hors activités). */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?string $budgetPerPerson = null;

    #[ORM\ManyToOne(inversedBy: 'ownedTrips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, TripMember>
     */
    #[ORM\OneToMany(targetEntity: TripMember::class, mappedBy: 'trip', cascade: ['persist'], orphanRemoval: true)]
    private Collection $members;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'trip', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['startAt' => 'ASC'])]
    private Collection $activities;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'trip', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['paidAt' => 'ASC'])]
    private Collection $expenses;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDurationInDays(): int
    {
        if (null === $this->startDate || null === $this->endDate) {
            return 0;
        }

        return $this->startDate->diff($this->endDate)->days + 1;
    }

    public function isPast(): bool
    {
        return null !== $this->endDate && $this->endDate < new \DateTimeImmutable('today');
    }

    public function isOngoing(): bool
    {
        $today = new \DateTimeImmutable('today');

        return null !== $this->startDate && null !== $this->endDate
            && $this->startDate <= $today && $this->endDate >= $today;
    }

    public function getBudgetPerPerson(): ?string
    {
        return $this->budgetPerPerson;
    }

    public function setBudgetPerPerson(?string $budgetPerPerson): static
    {
        $this->budgetPerPerson = $budgetPerPerson;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owner === $user;
    }

    /**
     * @return Collection<int, TripMember>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * Membres ayant accepté de participer (owner inclus).
     *
     * @return Collection<int, TripMember>
     */
    public function getAcceptedMembers(): Collection
    {
        return $this->members->filter(static fn (TripMember $m): bool => TripMemberStatus::Accepted === $m->getStatus());
    }

    public function getMemberFor(User $user): ?TripMember
    {
        foreach ($this->members as $member) {
            if ($member->getUser() === $user) {
                return $member;
            }
        }

        return null;
    }

    public function addMember(TripMember $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setTrip($this);
        }

        return $this;
    }

    public function removeMember(TripMember $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setTrip($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        $this->activities->removeElement($activity);

        return $this;
    }

    /** Somme des prix par personne de toutes les activités. */
    public function getActivitiesCostPerPerson(): float
    {
        $total = 0.0;
        foreach ($this->activities as $activity) {
            $total += (float) $activity->getPricePerPerson();
        }

        return $total;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setTrip($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        $this->expenses->removeElement($expense);

        return $this;
    }

    public function getExpensesTotal(): float
    {
        $total = 0.0;
        foreach ($this->expenses as $expense) {
            $total += (float) $expense->getAmount();
        }

        return $total;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
