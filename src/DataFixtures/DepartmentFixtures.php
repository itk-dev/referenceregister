<?php

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Department\ContactPerson;
use App\Entity\Department\LookupSlot;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class DepartmentFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $names = [
            'Department 1',
            'Department 2',
            'Department 3',
        ];
        foreach ($names as $index => $name) {
            $department = new Department()
                ->setName($name)
                ->setLookupSlot(new LookupSlot()
                    ->setStartsAt(match ($index) {
                        0 => LookupSlot::STARTS_AT_24_HOURS_AGO,
                        default => LookupSlot::STARTS_AT_MIDNIGHT,
                    })
                    ->setMaxLookups(2 ** $index + 1)
                )
            ;
            $contactPerson = new ContactPerson()
                ->setName('Contact 1')
                ->setEmail('contact0@department1.example.com')
                ->setPhone('0123456789')
                ->setDepartment($department);
            $manager->persist($department);
            $manager->persist($contactPerson);
            $this->addReference(sprintf('department:%s', $name), $department);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [
            'app',
            'department',
        ];
    }
}
