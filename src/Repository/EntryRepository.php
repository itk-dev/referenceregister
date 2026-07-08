<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entry>
 */
class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    /**
     * @return Entry[]
     */
    public function findByHash(string $hash, ?Department $department = null, bool $includeExpired = false): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.hash = :hash')
            ->setParameter('hash', $hash);
        if (null !== $department) {
            $qb->andWhere('e.department = :department')
                ->setParameter('department', $department);
        }
        if (!$includeExpired) {
            $qb->andWhere('e.expiredAt > :now')
                ->setParameter('now', new \DateTimeImmutable());
        }
        $query = $qb->getQuery();

        return $query->execute();
    }
}
