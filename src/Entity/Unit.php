<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribut\Sortable;
use App\Repository\UnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UnitRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Unit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:unit:admin', 'read:unit:user', 'read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?int $id = null;

    #[ORM\Column(length: 35)]
    #[Assert\NotBlank(message: 'The name of a unit can not be blank')]
    #[Assert\Length(max: 35, maxMessage: 'The name of a unit can not exceed {{ limit }} characters')]
    #[Groups(['read:unit:admin', 'read:unit:user', 'read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read:unit:admin', 'read:unit:user', 'read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:unit:admin', 'read:unit:user', 'read:flashcard:admin', 'read:flashcard:user'])]
    #[Sortable]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'units')]
    #[Assert\NotBlank(message: 'You must associate a topic to this unit')]
    #[Groups(['read:unit:admin', 'read:unit:user'])]
    private ?Topic $topic = null;

    #[ORM\OneToMany(mappedBy: 'unit', targetEntity: Flashcard::class, cascade: ['remove'])]
    private Collection $flashcards;

    public function __construct()
    {
        $this->flashcards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @return Collection<int, Flashcard>
     */
    public function getFlashcards(): Collection
    {
        return $this->flashcards;
    }

    public function addFlashcard(Flashcard $flashcard): static
    {
        if (!$this->flashcards->contains($flashcard)) {
            $this->flashcards->add($flashcard);
            $flashcard->setUnit($this);
        }

        return $this;
    }

    public function removeFlashcard(Flashcard $flashcard): static
    {
        if ($this->flashcards->removeElement($flashcard)) {
            // set the owning side to null (unless already changed)
            if ($flashcard->getUnit() === $this) {
                $flashcard->setUnit(null);
            }
        }

        return $this;
    }

    #[Groups(['read:unit:admin', 'read:unit:user'])]
    public function getCountFlashcards()
    {
        return $this->flashcards->count();
    }
}
