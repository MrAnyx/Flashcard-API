<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribut\Sortable;
use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:topic:admin', 'read:topic:user', 'read:unit:admin', 'read:unit:user'])]
    #[Sortable]
    private ?int $id = null;

    #[ORM\Column(length: 35)]
    #[Assert\NotBlank(message: 'The name of a topic can not be blank')]
    #[Assert\Length(max: 35, maxMessage: 'The name of a topic can not exceed {{ limit }} characters')]
    #[Groups(['read:topic:admin', 'read:topic:user', 'read:unit:admin', 'read:unit:user'])]
    #[Sortable]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read:topic:admin', 'read:topic:user', 'read:unit:admin', 'read:unit:user'])]
    #[Sortable]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:topic:admin', 'read:topic:user', 'read:unit:admin', 'read:unit:user'])]
    #[Sortable]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'topics')]
    #[Assert\NotBlank(message: 'You must associate a user to this topic')]
    #[Groups(['read:topic:admin'])]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: Unit::class, cascade: ['remove'])]
    private Collection $units;

    #[ORM\Column(length: 300)]
    #[Assert\Length(max: 300, maxMessage: 'The description of a topic can not exceed {{ limit }} characters')]
    #[Groups(['read:topic:admin', 'read:topic:user'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->units = new ArrayCollection();
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Unit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): static
    {
        if (!$this->units->contains($unit)) {
            $this->units->add($unit);
            $unit->setTopic($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): static
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getTopic() === $this) {
                $unit->setTopic(null);
            }
        }

        return $this;
    }

    #[Groups(['read:topic:admin', 'read:topic:user'])]
    public function getCountUnits()
    {
        return $this->units->count();
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
}
