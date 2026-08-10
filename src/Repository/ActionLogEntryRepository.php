<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActionLogEntry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends ServiceEntityRepository<ActionLogEntry>
 */
class ActionLogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionLogEntry::class);
    }

    public function findLookups(User $user, \DateTimeImmutable $since)
    {
        return $this
            ->createQueryBuilder('e')
            ->where('e.type = :type')
            ->setParameter('type', ActionLogEntry\Type::EntryLookUp)
            ->andWhere('e.createdBy = :user')
            ->setParameter('user', $user->getId(), UuidType::NAME)
            ->andWhere('e.createdAt >= :since')
            ->setParameter('since', $since)
            ->getQuery()
            ->execute();
    }
}
