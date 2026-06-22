<?php

namespace App\DataFixtures;

use App\EntryManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class EntryFixtures extends Fixture
{
    public function __construct(
        private readonly EntryManager $manager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $ids = [
            '0000000000',
            '123',
        ];
        foreach ($ids as $id) {
            $this->manager->addEntry($id);
        }
        $manager->flush();
    }
}
