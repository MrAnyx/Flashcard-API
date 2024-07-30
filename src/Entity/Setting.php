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
    public const DEFAULT_SETTINGS = [
        [
            'name' => SettingName::ITEMS_PER_PAGE,
            'type' => SettingType::INTEGER,
            'value' => 50,
        ],
        [
            'name' => SettingName::FLASHCARD_PER_SESSION,
            'type' => SettingType::INTEGER,
            'value' => 10,
        ],
    ];

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

    public function setValue(mixed $value): static
    {
        switch (\gettype($value)) {
            case 'integer':
                $this->setType(SettingType::INTEGER);
                $this->value = (string) $value;
                break;
            case 'boolean':
                $this->setType(SettingType::BOOLEAN);
                $this->value = (string) $value;
                break;
            case 'float':
            case 'double':
                $this->setType(SettingType::FLOAT);
                $this->value = (string) $value;
                break;
            case 'array':
            case 'object':
                $this->setType(SettingType::ITERABLE);
                $this->value = json_encode($value);
                break;
            case 'string':
            default:
                $this->setType(SettingType::STRING);
                $this->value = (string) $value;
                break;
        }

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

    /**
     * Should not be used outwide of this class.
     */
    private function setType(SettingType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDefaultSettings(): array
    {
        $defaultSettings = [];

        foreach (self::DEFAULT_SETTINGS as $defaultSetting) {
            $defaultSettings[$defaultSetting['name']->value] = $defaultSetting['value'];
        }

        return $defaultSettings;
    }
}
