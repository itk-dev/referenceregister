<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Department;
use App\EntryManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class EntryFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        private readonly EntryManager $manager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager->addEntry('0000000000', $this->getReference('department:Department 1', Department::class));

        $this->manager->addEntry('0000000001', $this->getReference('department:Department 2', Department::class));

        $this->manager->addEntry('0000000002', $this->getReference('department:Department 3', Department::class));

        $this->manager->addEntry('0000000002', $this->getReference('department:Department 2', Department::class));

        $entry = $this->manager->addEntry('expired', $this->getReference('department:Department 2', Department::class));
        $entry->setExpiredAt(new \DateTimeImmutable('-1 year'));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DepartmentFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return [
            'app',
        ];
    }
}
