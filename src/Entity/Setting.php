<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\SettingName;
use App\Repository\SettingRepository;
use App\Setting\SettingEntry;
use App\Setting\SettingTemplate;
use App\Setting\Type\SettingTypeInterface;
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
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'settings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $type = null;

    public function __construct(SettingEntry $settingEntry, User $user)
    {
        $this
            ->setName($settingEntry->getName(true))
            ->setValue($settingEntry->getSerializedValue())
            ->setType($settingEntry->getType()::class)
            ->setUser($user);
    }

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
        if ($this->type === null || !class_exists($this->type) || !is_a($this->type, SettingTypeInterface::class, true)) {
            throw new \RuntimeException(\sprintf('Type %s is not a valid type. It must implement SettingTypeInterface', $this->type));
        }

        if ($this->value === null) {
            throw new \RuntimeException('The value must be defined');
        }

        /** @var SettingTypeInterface $type */
        $type = new $this->type();

        return $type->deserialize($this->value, SettingTemplate::getSetting($this->name)?->getOptions() ?? []);
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
