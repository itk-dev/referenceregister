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
        $email = 'admin@example.com';
        $user = new User()
            ->setEmail($email)
            ->setRoles([Role::Administrator->value]);
        $manager->persist($user);

        $email = 'manager@department1.example.com';
        $user = new User()
            ->setEmail($email)
            ->setRoles([Role::Manager->value])
            ->addDepartment($this->getReference('department:Department 1', Department::class));
        $manager->persist($user);
        $this->addReference($email, $user);

        $email = 'manager@department2.example.com';
        $user = new User()
            ->setEmail($email)
            ->setRoles([Role::Manager->value])
            ->addDepartment($this->getReference('department:Department 2', Department::class));
        $manager->persist($user);
        $this->addReference($email, $user);

        $email = 'user@department1.example.com';
        $user = new User()
            ->setEmail($email)
            ->setRoles([Role::User->value])
            ->addDepartment($this->getReference('department:Department 1', Department::class));
        $manager->persist($user);
        $this->addReference($email, $user);

        $email = 'user@department2.example.com';
        $user = new User()
            ->setEmail($email)
            ->setRoles([Role::User->value])
            ->addDepartment($this->getReference('department:Department 2', Department::class));
        $manager->persist($user);
        $this->addReference($email, $user);

        $email = 'user@department3.example.com';
        $user = new User()
            ->setEmail($email)
            ->setRoles([Role::User->value])
            ->addDepartment($this->getReference('department:Department 3', Department::class));
        $manager->persist($user);
        $this->addReference($email, $user);

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
