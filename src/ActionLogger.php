<?php

declare(strict_types=1);

namespace App;

use App\Entity\ActionLogEntry;
use App\Entity\ActionLogEntry\Type;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ActionLogger
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function log(Type $type, array $context): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $entry = new ActionLogEntry($type, $context, $user);

        $this->entityManager->persist($entry);
        $this->entityManager->flush();
    }
}
