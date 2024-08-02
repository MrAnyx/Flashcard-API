<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\SettingName;
use App\Enum\SettingType;
use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: SettingName::class)]
    private ?SettingName $name = null;

    #[ORM\Column(type: Types::STRING, length: 1000)]
    private mixed $value = null;

    #[ORM\ManyToOne(inversedBy: 'settings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(enumType: SettingType::class)]
    private ?SettingType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?SettingName
    {
        return $this->name;
    }

    public function setName(SettingName $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): mixed
    {
        switch ($this->type) {
            case SettingType::STRING:
                return (string) $this->value;
            case SettingType::INTEGER:
                return (int) $this->value;
            case SettingType::FLOAT:
                return (float) $this->value;
            case SettingType::BOOLEAN:
                return filter_var($this->value, \FILTER_VALIDATE_BOOLEAN);
            case SettingType::ITERABLE:
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

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

    public function getType(): ?SettingType
    {
        return $this->type;
    }

    public function setType(SettingType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
