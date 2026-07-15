<?php

namespace App;

use App\Entity\ActionLogEntry\Type;
use App\Entity\Department;
use App\Entity\Entry;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use ItkDev\CprValidator\CprValidator;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class EntryManager
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly EntryRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Settings $settings,
        LoggerInterface $logger,
        private readonly ActionLogger $actionLogger,
    ) {
        $this->setLogger($logger);
    }

    public function addEntry(string $identifier, Department $department): Entry
    {
        $this->actionLogger->log(Type::EntryAdd, []);

        $entry = $this->getEntry($identifier, $department);
        if (null === $entry) {
            $entry = new Entry()
                ->setDepartment($department)
                ->setHash($this->hashIdentifier($identifier));
            $entry->setExpiredAt($this->computeExpiredAt($entry));

            $this->entityManager->persist($entry);
        }

        // @todo Update expiredAt on (existing) entry?
        $this->entityManager->flush();

        return $entry;
    }

    /**
     * @return Entry[]
     */
    public function lookUp(string $identifier): array
    {
        $this->actionLogger->log(Type::EntryLookUp, []);

        return $this->getEntries($identifier);
    }

    public function removeEntry(string $identifier, Department $department): bool
    {
        $this->actionLogger->log(Type::EntryRemove, []);

        $entries = $this->getEntries($identifier, $department, includeExpired: true);
        foreach ($entries as $entry) {
            $this->entityManager->remove($entry);
        }
        $this->entityManager->flush();

        return true;
    }

    public function isValidIdentifier(string $identifier): bool
    {
        $validator = new CprValidator();

        return $validator->isCpr($identifier);
    }

    /**
     * @return Entry[]
     */
    private function getEntries(string $identifier, ?Department $department = null, ?bool $includeExpired = false): array
    {
        $hash = $this->hashIdentifier($identifier);

        return $this->repository->findByHashAndDepartment($hash, $department, includeExpired: $includeExpired);
    }

    private function getEntry(string $identifier, Department $department): ?Entry
    {
        $hash = $this->hashIdentifier($identifier);

        return $this->repository->findOneBy([
            'hash' => $hash,
            'department' => $department,
        ]);
    }

    protected function hashIdentifier(string $identifier): string
    {
        return hash('sha512', $identifier);
    }

    private function computeExpiredAt(Entry $entry): \DateTimeImmutable
    {
        $entryExpiresAfterSpec = $this->settings->get('entry_expires_after');

        return new \DateTimeImmutable($entryExpiresAfterSpec);
    }

    /**
     * Load (hydrate) entries.
     *
     * @param Entry[] $entries
     *
     * @return Entry[]
     */
    public function loadEntries(array $entries): array
    {
        $ids = array_map(fn (Entry $entry) => $entry->getId(), $entries);

        return $this->repository->findBy(['id' => $ids]);
    }

    public function deleteExpired(?\DateTimeImmutable $now = null, ?bool $dryRun = false): void
    {
        $entries = $this->repository->findExpired($now);
        $count = count($entries);

        $this->logger->info(
            1 === $count
                ? 'One expired entry found.'
                : '{count} expired entries found.',
            ['count' => count($entries)]
        );

        foreach ($entries as $entry) {
            if ($dryRun) {
                $this->logger->info('Entry {entry} expired at {expired_at} will be deleted', ['entry' => $entry, 'expired_at' => $entry->getExpiredAt()]);
            } else {
                $this->logger->info('Deleting entry {entry} expired at {expired_at}', ['entry' => $entry, 'expired_at' => $entry->getExpiredAt()]);
                $this->entityManager->remove($entry);
            }
        }
        $this->entityManager->flush();

        $this->logger->info('All expired entries deleted.');
    }
}
