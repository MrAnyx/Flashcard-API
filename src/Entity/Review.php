<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\GradeType;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviewHistory')]
    #[Assert\NotBlank(message: 'You must associate a flashcard to this review')]
    private ?Flashcard $flashcard = null;

    #[ORM\ManyToOne(inversedBy: 'reviewHistory')]
    #[Assert\NotBlank(message: 'You must associate a user to this review')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::INTEGER, enumType: GradeType::class)]
    #[Assert\NotBlank(message: 'The grade of a review can not be blank')]
    private ?GradeType $grade = null;

    #[ORM\Column]
    private bool $reset = false;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    #[ORM\PrePersist]
    public function setDate(): static
    {
        $this->date = new \DateTimeImmutable('now');

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
}
