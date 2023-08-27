<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Attribut\Sortable;
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
    #[Groups(['read:flashcard:admin'])]
    #[Sortable]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:flashcard:admin'])]
    #[Sortable]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:flashcard:admin'])]
    #[Sortable]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The front side of a flashcard can not be blank')]
    #[Assert\Length(max: 255, maxMessage: 'The front side of a flashcard can not exceed {{ limit }} characters')]
    #[Groups(['read:flashcard:admin'])]
    private ?string $front = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The back side of a flashcard can not be blank')]
    #[Assert\Length(max: 255, maxMessage: 'The back side of a flashcard can not exceed {{ limit }} characters')]
    #[Groups(['read:flashcard:admin'])]
    private ?string $back = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'The details of a flashcard can not exceed {{ limit }} characters')]
    #[Groups(['read:flashcard:admin'])]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'flashcards')]
    #[Assert\NotBlank(message: 'You must associate a unit to this flashcard')]
    #[Groups(['read:flashcard:admin'])]
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
    public function setCreatedAt(): self
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
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTimeImmutable('now');

        return $this;
    }

    public function getFront(): ?string
    {
        return $this->front;
    }

    public function setFront(string $front): self
    {
        $this->front = $front;

        return $this;
    }

    public function getBack(): ?string
    {
        return $this->back;
    }

    public function setBack(string $back): self
    {
        $this->back = $back;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

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
