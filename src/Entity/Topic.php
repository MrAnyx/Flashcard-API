<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Attribut\Sortable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TopicRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:topic:admin'])]
    #[Sortable]
    private ?int $id = null;

    #[ORM\Column(length: 35)]
    #[Assert\NotBlank(message: 'The name of a topic can not be blank')]
    #[Assert\Length(max: 35, maxMessage: 'The name of a topic can not exceed {{ limit }} characters')]
    #[Groups(['read:topic:admin'])]
    #[Sortable]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read:topic:admin'])]
    #[Sortable]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:topic:admin'])]
    #[Sortable]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'topics')]
    #[Assert\NotBlank(message: 'You must associate a user to this topic')]
    #[Groups(['read:topic:admin'])]
    private ?User $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
