<?php

namespace App;

use App\Entity\Department;
use App\Entity\Entry;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EntryManager
{
    public function __construct(
        private readonly EntryRepository $repository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Settings $settings,
        #[Autowire(param: 'app_do_not_hash_entry_id')]
        private readonly bool $doNotHashEntryId,
    ) {
    }

    public function addEntry(Entry $entry): Entry
    {
        $id = $entry->getHash();
        $department = $entry->getDepartment();
        $entry = $this->getEntry($id, $department);
        if (null === $entry) {
            $entry = new Entry()
                ->setDepartment($department)
                ->setHash($this->hashId($id));
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
    public function lookUp(Entry $entry): array
    {
        $id = $entry->getHash();

        return $this->getEntries($id);
    }

    public function removeEntry(Entry $entry): bool
    {
        $id = $entry->getHash();
        $department = $entry->getDepartment();
        $entries = $this->getEntries($id, $department);
        foreach ($entries as $entry) {
            $this->entityManager->remove($entry);
        }
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return Entry[]
     */
    private function getEntries(string $id, ?Department $department = null): array
    {
        $hash = $this->hashId($id);

        return $this->repository->findByHash($hash, $department);
    }

    private function getEntry(string $id, Department $department): ?Entry
    {
        $hash = $this->hashId($id);

        return $this->repository->findOneBy([
            'hash' => $hash,
            'department' => $department,
        ]);
    }

    private function hashId(string $id): string
    {
        if ($this->doNotHashEntryId) {
            return $id;
        }

        return hash('sha512', $id);
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
}
