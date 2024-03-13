<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribut\Sortable;
use App\Enum\StateType;
use App\Repository\FlashcardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
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
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?\DateTimeImmutable $updatedAt = null;

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
    private ?\DateTimeImmutable $nextReview = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?\DateTimeImmutable $previousReview = null;

    #[ORM\Column(type: Types::INTEGER, enumType: StateType::class)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    #[Assert\NotBlank(message: 'The state of a flashcard can not be blank')]
    private StateType $state = StateType::New;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'The difficulty must be between {{ min }} and {{ max }}',
    )]
    private ?float $difficulty = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?float $stability = null;

    #[ORM\ManyToOne(inversedBy: 'flashcards')]
    #[Assert\NotBlank(message: 'You must associate a unit to this flashcard')]
    #[Groups(['read:flashcard:admin', 'read:flashcard:user'])]
    private ?Unit $unit = null;

    #[ORM\OneToMany(mappedBy: 'flashcard', targetEntity: Review::class, cascade: ['remove'])]
    private Collection $reviewHistory;

    public function __construct()
    {
        $this->reviewHistory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

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

    public function getNextReview(): ?\DateTimeImmutable
    {
        return $this->nextReview;
    }

    public function setNextReview(?\DateTimeImmutable $nextReview): static
    {
        $this->nextReview = $nextReview;

        return $this;
    }

    public function getPreviousReview(): ?\DateTimeImmutable
    {
        return $this->previousReview;
    }

    public function setPreviousReview(?\DateTimeImmutable $previousReview): static
    {
        $this->previousReview = $previousReview;

        return $this;
    }

    public function refreshPreviousReview(): static
    {
        $this->previousReview = new \DateTimeImmutable();

        return $this;
    }

    public function getState(): StateType
    {
        return $this->state;
    }

    public function setState(StateType $state): static
    {
        $this->state = $state;

        return $this;
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

    /**
     * @return Collection<int, Review>
     */
    public function getReviewHistory(): Collection
    {
        return $this->reviewHistory;
    }

    public function addReviewHistory(Review $reviewHistory): static
    {
        if (!$this->reviewHistory->contains($reviewHistory)) {
            $this->reviewHistory->add($reviewHistory);
            $reviewHistory->setFlashcard($this);
        }

        return $this;
    }

    public function removeReviewHistory(Review $reviewHistory): static
    {
        if ($this->reviewHistory->removeElement($reviewHistory)) {
            // set the owning side to null (unless already changed)
            if ($reviewHistory->getFlashcard() === $this) {
                $reviewHistory->setFlashcard(null);
            }
        }

        return $this;
    }
}
