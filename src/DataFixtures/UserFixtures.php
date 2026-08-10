<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = new User()
            ->setEmail('admin@example.com')
            ->setRoles([Role::Administrator->value]);
        $manager->persist($user);

        $user = new User()
            ->setEmail('manager@department1.example.com')
            ->setRoles([Role::Manager->value])
            ->addDepartment($this->getReference('department:Department 1', Department::class));
        $manager->persist($user);
        $this->addReference($user->getEmail(), $user);

        $user = new User()
            ->setEmail('manager@department2.example.com')
            ->setRoles([Role::Manager->value])
            ->addDepartment($this->getReference('department:Department 2', Department::class));
        $manager->persist($user);
        $this->addReference($user->getEmail(), $user);

        $user = new User()
            ->setEmail('user@department1.example.com')
            ->setRoles([Role::User->value])
            ->addDepartment($this->getReference('department:Department 1', Department::class));
        $manager->persist($user);
        $this->addReference($user->getEmail(), $user);

        $user = new User()
            ->setEmail('user@department2.example.com')
            ->setRoles([Role::User->value])
            ->addDepartment($this->getReference('department:Department 2', Department::class));
        $manager->persist($user);
        $this->addReference($user->getEmail(), $user);

        $user = new User()
            ->setEmail('user@department3.example.com')
            ->setRoles([Role::User->value])
            ->addDepartment($this->getReference('department:Department 3', Department::class));
        $manager->persist($user);
        $this->addReference($user->getEmail(), $user);

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
            'user',
        ];
    }
}
