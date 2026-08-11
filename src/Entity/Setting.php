<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @phpstan-type Config array<string, mixed>
 */
#[ORM\Entity(repositoryClass: SettingRepository::class)]
#[UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
#[UniqueEntity('name')]
class Setting
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private string $type;

    /** @var Config */
    #[ORM\Column(type: Types::JSON)]
    private array $config = [];

    #[ORM\Column(type: Types::JSON)]
    private mixed $value = [];

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getFormType(): ?string
    {
        $config = $this->getConfig();
        if (!array_key_exists('form_type', $config) || !is_string($config['form_type'])) {
            return null;
        }

        return $config['form_type'];
    }

    /**
     * @return Config|null
     */
    public function getFormTypeOptions(): ?array
    {
        $config = $this->getConfig();
        if (!array_key_exists('form_type_options', $config) || !is_array($config['form_type_options'])) {
            return null;
        }

        // @mago-ignore analysis:less-specific-return-statement
        return $config['form_type_options'];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }
}
