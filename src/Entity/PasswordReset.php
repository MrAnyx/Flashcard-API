<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PasswordResetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PasswordResetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PasswordReset
{
    /**
     * Algorithm used to hash the token in the database.
     */
    public const TOKEN_HASH_ALGO = 'sha512';

    /**
     * Number of minutes before the token expires.
     */
    public const DEFAULT_TOKEN_EXPIRATION = 10;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'You must associate a user to this password reset request')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $expirationDate = null;

    #[ORM\Column(length: 128)]
    #[Assert\Length(exactly: 128, message: 'The token is not valid')]
    private ?string $token = null;

    #[ORM\Column]
    private ?bool $used = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    #[ORM\PrePersist]
    public function setExpirationDate(): static
    {
        $expirationInterval = self::DEFAULT_TOKEN_EXPIRATION;
        $this->expirationDate = (new \DateTimeImmutable())->modify("+{$expirationInterval} minutes");

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = hash(self::TOKEN_HASH_ALGO, $token);

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): static
    {
        $this->used = $used;

        return $this;
    }
}
