<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\GradeType;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:review:user'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviewHistory')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'You must associate a flashcard to this review')]
    #[Groups(['read:review:user'])]
    private ?Flashcard $flashcard = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    #[Groups(['read:review:user'])]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::INTEGER, enumType: GradeType::class)]
    #[Assert\NotBlank(message: 'The grade of a review can not be blank')]
    #[Groups(['read:review:user', 'write:review:user'])]
    private ?GradeType $grade = null;

    #[ORM\Column]
    #[Groups(['read:review:user'])]
    private bool $reset = false;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'You must associate a session to this review')]
    #[Groups(['write:review:user'])]
    private ?Session $session = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'The difficulty must be between {{ min }} and {{ max }}',
    )]
    private ?float $difficulty = null;

    #[ORM\Column(nullable: true)]
    private ?float $stability = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlashcard(): ?Flashcard
    {
        return $this->flashcard;
    }

    public function setFlashcard(?Flashcard $flashcard): static
    {
        $this->flashcard = $flashcard;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    #[ORM\PrePersist]
    public function setDate(): static
    {
        $this->date = new \DateTimeImmutable();

        return $this;
    }

    public function getGrade(): ?GradeType
    {
        return $this->grade;
    }

    public function setGrade(GradeType $grade): static
    {
        $this->grade = $grade;

        return $this;
    }

    public function isReset(): bool
    {
        return $this->reset;
    }

    public function setReset(bool $reset): static
    {
        $this->reset = $reset;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

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
}
