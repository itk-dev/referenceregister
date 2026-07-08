<?php

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Entry;
use App\EntryManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class EntryFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly EntryManager $manager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager->addEntry(new Entry()
            ->setHash('0000000000')
            ->setDepartment($this->getReference('department:Department 0', Department::class)));

        $this->manager->addEntry(new Entry()
            ->setHash('0000000001')
            ->setDepartment($this->getReference('department:Department 1', Department::class)));

        $this->manager->addEntry(new Entry()
            ->setHash('0000000002')
            ->setDepartment($this->getReference('department:Department 2', Department::class)));

        $this->manager->addEntry(new Entry()
            ->setHash('0000000002')
            ->setDepartment($this->getReference('department:Department 1', Department::class)));

        $entry = $this->manager->addEntry(new Entry()
            ->setHash('expired')
            ->setDepartment($this->getReference('department:Department 1', Department::class)));
        $entry->setExpiredAt(new \DateTimeImmutable('-1 year'));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DepartmentFixtures::class,
        ];
    }
}
