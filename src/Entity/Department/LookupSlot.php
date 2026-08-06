<?php

namespace App\Entity\Department;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

#[ORM\Embeddable()]
class LookupSlot
{
    public const int LOOKUPS_MIN = 1;
    public const int LOOKUPS_MAX = 99;

    public const string STARTS_AT_MIDNIGHT = 'midnight';
    public const string STARTS_AT_24_HOURS_AGO = '-24 hours';

    #[ORM\Column(length: 255)]
    #[NotBlank]
    private string $startsAt = self::STARTS_AT_MIDNIGHT;

    #[ORM\Column]
    #[Range(min: self::LOOKUPS_MIN, max: self::LOOKUPS_MAX)]
    private int $maxLookups = 5;

    public function getStartsAt(): string
    {
        return $this->startsAt;
    }

    public function setStartsAt(string $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getMaxLookups(): int
    {
        return $this->maxLookups;
    }

    public function setMaxLookups(int $maxLookups): static
    {
        $this->maxLookups = $maxLookups;

        return $this;
    }
}
