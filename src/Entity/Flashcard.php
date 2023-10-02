<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use App\Attribut\Sortable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FlashcardRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FlashcardRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Flashcard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The front side of a flashcard can not be blank')]
    #[Assert\Length(max: 255, maxMessage: 'The front side of a flashcard can not exceed {{ limit }} characters')]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?string $front = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The back side of a flashcard can not be blank')]
    #[Assert\Length(max: 255, maxMessage: 'The back side of a flashcard can not exceed {{ limit }} characters')]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?string $back = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'The details of a flashcard can not exceed {{ limit }} characters')]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?string $details = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?DateTime $nextReview = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?DateTime $previousReview = null;

    #[ORM\Column]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private int $reviews = 0;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?float $difficulty = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?float $stability = null;

    #[ORM\ManyToOne(inversedBy: 'flashcards')]
    #[Assert\NotBlank(message: 'You must associate a unit to this flashcard')]
    private ?Unit $unit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new DateTimeImmutable('now');

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new DateTimeImmutable('now');

        return $this;
    }

    public function getFront(): ?string
    {
        return $this->front;
    }

    public function setFront(string $front): static
    {
        $this->front = $front;

        return $this;
    }

    public function getBack(): ?string
    {
        return $this->back;
    }

    public function setBack(string $back): static
    {
        $this->back = $back;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getNextReview(): ?DateTime
    {
        return $this->nextReview;
    }

    public function setNextReview(?DateTime $nextReview): static
    {
        $this->nextReview = $nextReview;

        return $this;
    }

    public function getPreviousReview(): ?DateTime
    {
        return $this->previousReview;
    }

    public function setPreviousReview(?DateTime $previousReview): static
    {
        $this->previousReview = $previousReview;

        return $this;
    }

    public function refreshPreviousReview(): static
    {
        $this->previousReview = new DateTime;

        return $this;
    }

    public function getReviews(): int
    {
        return $this->reviews;
    }

    public function setReviews(int $reviews): static
    {
        $this->reviews = $reviews;

        return $this;
    }

    public function incrementReviews(): static
    {
        $this->reviews++;

        return $this;
    }

    public function isNew(): bool
    {
        return $this->getReviews() === 0;
    }

    public function getDifficulty(): ?float
    {
        return $this->difficulty;
    }

    public function setDifficulty(?float $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getStability(): ?float
    {
        return $this->stability;
    }

    public function setStability(?float $stability): static
    {
        $this->stability = $stability;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
