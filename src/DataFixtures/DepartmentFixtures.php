<?php

namespace App\DataFixtures;

use App\Entity\ContactPerson;
use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class DepartmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $names = [
            'Department 0',
            'Department 1',
            'Department 2',
        ];
        foreach ($names as $name) {
            $department = new Department()
                ->setName($name);
            $contactPerson = new ContactPerson()
                ->setName('Contact 0')
                ->setEmail('contact0@department1.example.com')
                ->setPhone('0123456789')
                ->setDepartment($department);
            $manager->persist($department);
            $manager->persist($contactPerson);
            $this->addReference(sprintf('department:%s', $name), $department);
        }
        $manager->flush();
    }
}
