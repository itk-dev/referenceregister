<?php

namespace App;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class EntryManager
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $hasher,
        private readonly EntryRepository $repository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function addEntry(string $id): bool
    {
        $entry = $this->getEntry($id);
        if (null === $entry) {
            $entry = new Entry();
            $entry->setHash($this->hashId($id));

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
        $entries = $this->repository->findAll();
        foreach ($entries as $entry) {
            if ($this->hasher->getPasswordHasher(Entry::class)->verify($entry->getHash(), $id)) {
                return $entry;
            }
        }

        return null;
    }

    private function hashId(string $id): string
    {
        return $this->hasher->getPasswordHasher(Entry::class)->hash($id);
    }
}
