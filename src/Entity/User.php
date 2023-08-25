<?php

namespace App\Entity;

use App\Utility\Regex;
use DateTimeImmutable;
use App\Attribut\Sortable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered. Please, use this email to login or use another email')]
#[UniqueEntity(fields: ['username'], message: 'This username is already registered. Please, use this username to login or use another username')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['read:user:admin', 'read:topic:admin', 'read:flashcard:admin'])]
    #[Sortable]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Your email can not be blank')]
    #[Assert\Email(message: 'Your email is invalid and doesn\'t respect the email format')]
    #[Assert\Length(max: 180, maxMessage: 'Your email can not exceed {{ limit }} characters')]
    #[Groups(['read:user:admin'])]
    #[Sortable]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 30, unique: true)]
    #[Assert\NotBlank(message: 'Your username can not be blank')]
    #[Assert\Length(max: 30, maxMessage: 'Your username can not exceed {{ limit }} characters')]
    #[Assert\Regex(pattern: Regex::USERNAME_SLASH, message: 'Your username must only contain letters, numbers, dots, dashes or underscores')]
    #[Groups(['read:user:admin', 'read:topic:admin', 'read:flashcard:admin'])]
    #[Sortable]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['read:user:admin'])]
    #[Sortable]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['read:user:admin'])]
    #[Sortable]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['read:user:admin'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'Your password can not be blank', groups: ['edit:user:password'])]
    #[Assert\NotCompromisedPassword(message: 'This password has been compromised. Please choose another password', groups: ['edit:user:password'])]
    #[Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_VERY_STRONG, message: 'You must choose a stronger password', groups: ['edit:user:password'])]
    #[Assert\NotEqualTo(propertyPath: 'username', message: 'You must choose a stronger password', groups: ['edit:user:password'])]
    #[Assert\NotEqualTo(propertyPath: 'email', message: 'You must choose a stronger password', groups: ['edit:user:password'])]
    private ?string $rawPassword = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Flashcard::class, cascade: ['remove'])]
    private Collection $flashcards;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Topic::class, cascade: ['remove'])]
    private Collection $topics;

    public function __construct()
    {
        $this->flashcards = new ArrayCollection();
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getRawPassword(): ?string
    {
        return $this->rawPassword;
    }

    public function setRawPassword(?string $rawPassword): self
    {
        $this->rawPassword = $rawPassword;

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
        if (! $this->flashcards->contains($flashcard)) {
            $this->flashcards->add($flashcard);
            $flashcard->setAuthor($this);
        }

        return $this;
    }

    public function removeFlashcard(Flashcard $flashcard): static
    {
        if ($this->flashcards->removeElement($flashcard)) {
            // set the owning side to null (unless already changed)
            if ($flashcard->getAuthor() === $this) {
                $flashcard->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        if (! $this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setAuthor($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getAuthor() === $this) {
                $topic->setAuthor(null);
            }
        }

        return $this;
    }
}
