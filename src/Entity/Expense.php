<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dépense partagée pendant un voyage (qui a payé quoi, pour qui) — façon Tricount.
 */
#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Trip $trip = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Positive]
    private string $amount = '0.00';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $paidBy = null;

    /** Optionnel : dépense liée à une activité. */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Activity $activity = null;

    /**
     * Membres entre qui la dépense est partagée.
     *
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'expense_shared_with')]
    private Collection $sharedWith;

    public function __construct()
    {
        $this->sharedWith = new ArrayCollection();
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): static
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getPaidBy(): ?User
    {
        return $this->paidBy;
    }

    public function setPaidBy(?User $paidBy): static
    {
        $this->paidBy = $paidBy;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getSharedWith(): Collection
    {
        return $this->sharedWith;
    }

    public function addSharedWith(User $user): static
    {
        if (!$this->sharedWith->contains($user)) {
            $this->sharedWith->add($user);
        }

        return $this;
    }

    public function removeSharedWith(User $user): static
    {
        $this->sharedWith->removeElement($user);

        return $this;
    }

    public function getAmountPerPerson(): float
    {
        $count = $this->sharedWith->count();

        return $count > 0 ? (float) $this->amount / $count : (float) $this->amount;
    }
}
