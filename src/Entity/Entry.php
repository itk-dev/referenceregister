<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EntryRepository::class)]
#[ORM\Index(columns: ['hash'], name: 'entry_hash_idx')]
class Entry implements \Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\ManyToOne(inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiredAt = null;

    #[\Override]
    public function __toString(): string
    {
        return sprintf('%s (#%s)', self::class, $this->getId());
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeImmutable $expiredAt): static
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }
}
