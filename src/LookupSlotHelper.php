<?php

namespace App;

use App\Entity\User;
use App\Exception\LogicException;
use App\Model\LookupSlotInfo;
use App\Repository\ActionLogEntryRepository;

class LookupSlotHelper
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly ActionLogEntryRepository $actionLogEntryRepository,
    ) {
    }

    public function getLookupSlot(User $user): LookupSlotInfo
    {
        $departments = $this->userManager->getUserDepartments($user);
        // It's not obvious which lookup slot should be used if the user has
        // multiple departments. Therefore we just use the first (for now).
        $department = reset($departments);
        $lookupSlot = $department->getLookupSlot();
        if (null === $lookupSlot) {
            throw new LogicException('Cannot get lookup slot');
        }

        try {
            $startsAt = new \DateTimeImmutable($lookupSlot->getStartsAt());
        } catch (\Exception) {
            throw new LogicException('Cannot compute lookup slot start time');
        }
        // @todo Compute ends at (if it makes sense). It makes sense for "midnight".
        $endsAt = null;

        $used = count($this->actionLogEntryRepository->findLookups($user, $startsAt));

        return new LookupSlotInfo(
            startsAt: $startsAt,
            endsAt: $endsAt,
            used: $used,
            max: $lookupSlot->getMaxLookups(),
        );
    }
}
