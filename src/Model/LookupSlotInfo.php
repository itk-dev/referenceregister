<?php

namespace App\Model;

use App\Entity\Department;

final readonly class LookupSlotInfo
{
    public function __construct(
        public \DateTimeImmutable $startsAt,
        public ?\DateTimeImmutable $endsAt = null,
        public int $used = 0,
        public int $max = Department\LookupSlot::LOOKUPS_MAX,
    ) {
    }

    public function allowsLookup(): bool
    {
        return $this->used < $this->max;
    }
}
