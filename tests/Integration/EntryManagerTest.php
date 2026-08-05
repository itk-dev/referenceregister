<?php

namespace App\Tests\Integration;

use App\Entity\Department;
use App\Entity\Entry;
use App\EntryManager;
use App\Exception\InvalidIdentifierException;
use App\Repository\DepartmentRepository;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class EntryManagerTest extends KernelTestCase
{
    private EntryManager $entryManager;
    private DepartmentRepository $departmentRepository;
    private EntryRepository $entryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $this->entryManager = $container->get(EntryManager::class);
        $this->departmentRepository = $entityManager->getRepository(Department::class);
        $this->entryRepository = $entityManager->getRepository(Entry::class);
        // Make sure that all tests run with no existing entries.
        foreach ($this->entryRepository->findAll() as $entry) {
            $entityManager->remove($entry);
        }
        $entityManager->flush();
    }

    public function testSomething(): void
    {
        $department = $this->getDepartment('Department 1');
        $anotherDepartment = $this->getDepartment('Department 2');
        $entryManager = $this->entryManager;

        $identifier = '0000000000';
        $anotherIdentifier = '0000000001';

        $entry = $entryManager->addEntry($identifier, $department);
        $this->assertNotNull($entry->getId());

        $entries = $entryManager->lookUp($identifier);
        $this->assertCount(1, $entries);

        $entry = $entryManager->addEntry($identifier, $department);
        $this->assertNotNull($entry->getId());

        $entries = $entryManager->lookUp($identifier);
        $this->assertCount(1, $entries);

        $entry = $entryManager->addEntry($identifier, $anotherDepartment);
        $this->assertNotNull($entry->getId());

        $entries = $entryManager->lookUp($identifier);
        $this->assertCount(2, $entries);

        $result = $entryManager->removeEntry($identifier, $anotherDepartment);
        $this->assertTrue($result);

        $entries = $entryManager->lookUp($identifier);
        $this->assertCount(1, $entries);

        $entries = $entryManager->lookUp($anotherIdentifier);
        $this->assertCount(0, $entries);

        $result = $entryManager->removeEntry($anotherIdentifier, $anotherDepartment);
        $this->assertTrue($result);

        $entry = $entryManager->addEntry($anotherIdentifier, $anotherDepartment);
        $this->assertNotNull($entry->getId());

        $entries = $entryManager->lookUp($anotherIdentifier);
        $this->assertCount(1, $entries);

        $result = $entryManager->removeEntry($anotherIdentifier, $department);
        $this->assertTrue($result);

        $entries = $entryManager->lookUp($anotherIdentifier);
        $this->assertCount(1, $entries);

        $result = $entryManager->removeEntry($anotherIdentifier, $anotherDepartment);
        $this->assertTrue($result);

        $entries = $entryManager->lookUp($anotherIdentifier);
        $this->assertCount(0, $entries);

        $this->assertCount(1, $this->entryRepository->findAll());

        $result = $entryManager->removeEntry($identifier, $department);
        $this->assertTrue($result);

        $this->assertCount(0, $this->entryRepository->findAll());
    }

    public function testAddEntry(): void
    {
        $department = $this->getDepartment('Department 1');
        $entryManager = $this->entryManager;

        $entry = $entryManager->addEntry('test-123', $department);
        $this->assertNotNull($entry->getId());

        $this->assertCount(1, $this->entryRepository->findAll());
    }

    public function testCannotAddWithInvalidIdentifier(): void
    {
        $department = $this->getDepartment('Department 1');
        $entryManager = $this->entryManager;

        $this->expectException(InvalidIdentifierException::class);
        $entryManager->addEntry('invalid-identifier', $department);
    }

    public function testRemoveEntry(): void
    {
        $department = $this->getDepartment('Department 1');
        $entryManager = $this->entryManager;

        $entry = $entryManager->addEntry('test-123', $department);
        $this->assertNotNull($entry->getId());

        $result = $entryManager->removeEntry('test-123', $department);
        $this->assertTrue($result);

        $this->assertEntriesCount(0);
    }

    public function testRemoveNonExistingEntry(): void
    {
        $department = $this->getDepartment('Department 1');
        $entryManager = $this->entryManager;

        $entry = $entryManager->addEntry('test-123', $department);
        $this->assertNotNull($entry->getId());

        $result = $entryManager->removeEntry('test-1234', $department);
        $this->assertTrue($result);

        $this->assertEntriesCount(1);

        $result = $entryManager->removeEntry('test-123', $department);
        $this->assertTrue($result);

        $this->assertEntriesCount(0);
    }

    private function getDepartment(string $name): Department
    {
        $department = $this->departmentRepository->findOneBy(['name' => $name]);

        if (null === $department) {
            throw new \RuntimeException(sprintf('Cannot find department with name "%s"', $name));
        }

        return $department;
    }

    final public function assertEntriesCount(int $expectedCount, string $message = ''): void
    {
        $this->assertCount($expectedCount, $this->entryRepository->findAll());
    }
}
