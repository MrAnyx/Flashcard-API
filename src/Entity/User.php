<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Exception\ExceptionMessage;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: ExceptionMessage::EMAIL_DUPLICATION)]
#[UniqueEntity(fields: ['username'], message: ExceptionMessage::USERNAME_DUPLICATION)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Your email can not be blank')]
    #[Assert\Email(message: 'Your email is invalid and doesn\'t respect the email format')]
    #[Assert\Length(max: 180, maxMessage: 'Your email can not exceed {{ limit }} characters')]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 30, unique: true)]
    #[Assert\NotBlank(message: 'Your username can not be blank', payload: ['code' => 'test'])]
    #[Assert\Length(max: 30, maxMessage: 'Your username can not exceed {{ limit }} characters')]
    #[Assert\Regex(pattern: '/^[\w\-\.]*$/', message: 'Your username must only contain letters, numbers, dots, dashes or underscores')]
    private ?string $username = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: Types::STRING)]
    #[Ignore]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\NotCompromisedPassword(message: 'This password has been compromised. Please choose another password')]
    #[Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_VERY_STRONG, message: 'You must choose a stronger password')]
    #[Assert\NotEqualTo(propertyPath: 'username', message: 'You must choose a stronger password')]
    #[Assert\NotEqualTo(propertyPath: 'email', message: 'You must choose a stronger password')]
    #[Ignore]
    private ?string $rawPassword = null;

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
    #[Ignore]
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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
}
