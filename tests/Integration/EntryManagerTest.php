<?php

namespace App\Tests\Unit;

use App\Entity\Department;
use App\Entity\Entry;
use App\EntryManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntryManagerTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        /** @var EntryManager $manager */
        $manager = static::getContainer()->get(EntryManager::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $identifier = '0000000000';
        $anotherIdentifier = '0000000001';
        $department = new Department()
            ->setName('Test department');
        $entityManager->persist($department);
        $anotherDepartment = new Department()
            ->setName('Another test department');
        $entityManager->persist($anotherDepartment);
        $entityManager->flush();

        $entry = $manager->addEntry($identifier, $department);
        $this->assertNotNull($entry->getId());

        $entries = $manager->lookUp($identifier);
        $this->assertCount(1, $entries);

        $entry = $manager->addEntry($identifier, $department);
        $this->assertNotNull($entry->getId());

        $entries = $manager->lookUp($identifier);
        $this->assertCount(1, $entries);

        $entry = $manager->addEntry($identifier, $anotherDepartment);
        $this->assertNotNull($entry->getId());

        $entries = $manager->lookUp($identifier);
        $this->assertCount(2, $entries);

        $result = $manager->removeEntry($identifier, $anotherDepartment);
        $this->assertTrue($result);

        $entries = $manager->lookUp($identifier);
        $this->assertCount(1, $entries);

        $entries = $manager->lookUp($anotherIdentifier);
        $this->assertCount(0, $entries);

        $result = $manager->removeEntry($anotherIdentifier, $anotherDepartment);
        $this->assertTrue($result);

        $entry = $manager->addEntry($anotherIdentifier, $anotherDepartment);
        $this->assertNotNull($entry->getId());

        $entries = $manager->lookUp($anotherIdentifier);
        $this->assertCount(1, $entries);

        $result = $manager->removeEntry($anotherIdentifier, $department);
        $this->assertTrue($result);

        $entries = $manager->lookUp($anotherIdentifier);
        $this->assertCount(1, $entries);

        $result = $manager->removeEntry($anotherIdentifier, $anotherDepartment);
        $this->assertTrue($result);

        $entries = $manager->lookUp($anotherIdentifier);
        $this->assertCount(0, $entries);

        $this->assertCount(1, $entityManager->getRepository(Entry::class)->findAll());

        $result = $manager->removeEntry($identifier, $department);
        $this->assertTrue($result);

        $this->assertCount(0, $entityManager->getRepository(Entry::class)->findAll());
    }
}
