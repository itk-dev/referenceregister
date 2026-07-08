<?php

namespace App;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;

class EntryManager
{
    public function __construct(
        private readonly EntryRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function addEntry(string $id): bool
    {
        $entry = $this->getEntry($id);
        if (null === $entry) {
            $entry = new Entry()
                ->setHash($this->hashId($id));

            $this->entityManager->persist($entry);
            $this->entityManager->flush();
        }

        return true;
    }

    public function lookUpEntry(string $id): ?Entry
    {
        return $this->getEntry($id);
    }

    public function removeEntry(string $id): bool
    {
        $entry = $this->getEntry($id);
        if (null !== $entry) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();
        }

        return true;
    }

    private function getEntry(string $id): ?Entry
    {
        $hash = $this->hashId($id);

        return $this->repository->findOneBy(['hash' => $hash]);
    }

    private function hashId(string $id): string
    {
        return hash('sha512', $id);
    }
}
