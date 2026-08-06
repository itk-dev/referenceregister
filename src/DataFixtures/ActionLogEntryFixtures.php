<?php

namespace App\DataFixtures;

use App\Entity\ActionLogEntry;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ActionLogEntryFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $type = ActionLogEntry\Type::EntryLookUp;
        $user = $this->getReference('manager@department2.example.com', User::class);
        $entry = new ActionLogEntry($type, [], $user);
        $manager->persist($entry);

        // Use up all look-ups
        $type = ActionLogEntry\Type::EntryLookUp;
        $user = $this->getReference('user@department2.example.com', User::class);
        $entry = new ActionLogEntry($type, [], $user);
        $manager->persist($entry);

        $type = ActionLogEntry\Type::EntryLookUp;
        $user = $this->getReference('user@department2.example.com', User::class);
        $entry = new ActionLogEntry($type, [], $user);
        $manager->persist($entry);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return [
            'app',
        ];
    }
}
