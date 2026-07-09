<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

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
    public function findByHashAndDepartment(string $hash, ?Department $department, bool $includeExpired = false): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.hash = :hash')
            ->setParameter('hash', $hash);
        if (null !== $department) {
            $qb->andWhere('e.department = :department')
                ->setParameter('department', $department->getId(), UuidType::NAME);
        }
        if (!$includeExpired) {
            $qb->andWhere('e.expiredAt > :now')
                ->setParameter('now', new \DateTimeImmutable());
        }
        $query = $qb->getQuery();

        return $query->execute();
    }

    /**
     * @return Entry[]
     */
    public function findExpired(?\DateTimeImmutable $now = null): array
    {
        $now ??= new \DateTimeImmutable();

        $qb = $this->createQueryBuilder('e')
            ->where('e.expiredAt <= :now')
            ->setParameter('now', $now);

        return $qb->getQuery()->execute();
    }
}
