<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ActionLogEntry\Type;
use App\Repository\ActionLogEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ActionLogEntryRepository::class)]
class ActionLogEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null {
        get {
            return $this->id;
        }
    }

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt {
        get {
            return $this->createdAt;
        }
    }

    public function __construct(
        #[ORM\Column(enumType: Type::class)]
        public Type $type {
            get {
                return $this->type;
            }
        },

        /**
         * @var array<array-key, mixed>
         */
        #[ORM\Column]
        public array $context {
            get {
                return $this->context;
            }
        },

        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(referencedColumnName: 'id')]
        #[Gedmo\Blameable(on: 'create')]
        public ?User $createdBy = null {
            get {
                return $this->createdBy;
            }
        },
    ) {
    }
}
