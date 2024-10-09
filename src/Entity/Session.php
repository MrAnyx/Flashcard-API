<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribute\Searchable;
use App\Attribute\Sortable;
use App\Attribute\Virtual;
use App\FilterConverter\DateTimeConverter;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:session:user'])]
    #[Sortable]
    #[Searchable]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:session:user'])]
    #[Sortable]
    #[Searchable(DateTimeConverter::class, ['format' => \DateTimeInterface::ATOM])]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:session:user'])]
    #[Sortable]
    #[Searchable(DateTimeConverter::class, ['format' => \DateTimeInterface::ATOM])]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'You must associate a user to this session')]
    private ?User $author = null;

    #[Virtual('totalReviews')]
    #[Groups(['read:session:user'])]
    private ?int $totalReviews = null;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'session', orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    #[ORM\PrePersist]
    public function setStartedAt(): static
    {
        $this->startedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeImmutable $endedAt): static
    {
        $this->endedAt = $endedAt;

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
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setSession($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getSession() === $this) {
                $review->setSession(null);
            }
        }

        return $this;
    }

    public function getTotalReviews(): ?int
    {
        return $this->totalReviews;
    }

    public function setTotalReviews(int $totalReviews): static
    {
        $this->totalReviews = $totalReviews;

        return $this;
    }
}
