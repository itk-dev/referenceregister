<?php

namespace App\Tests\E2E;

use App\Repository\UserRepository;

class AppTest extends PantherTestCase
{
    public function testMyApp(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('manager@department1.example.com');

        $client = static::authenticateClient(static::createPantherClient(), $user);

        $client->request('GET', '/');
        $this->assertPageTitleContains('Referenceregister');
    }
}
