<?php

namespace App\Tests\Application;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\ApplicationTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppTest extends WebTestCase
{
    use ApplicationTestCaseTrait;

    public function testSomething(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepository->findOneByEmail('manager@department1.example.com');
        $client->loginUser($user);
        $this->assertCount(1, $user->getDepartments());

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Referenceregister');

        $client->clickLink('Look up');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Look up entry', [
            'entry_look_up_form[identifier]' => '123-hest',
        ]);
        $this->assertResponseIsUnprocessable();
        $this->assertSelectorTextContains('*', 'Invalid identifier');
        $client->submitForm('Look up entry', [
            'entry_look_up_form[identifier]' => '123',
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Entry not found');

        $client->clickLink('Remove entry');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(1, 'form');
        $this->assertSelectorCount($user->getDepartments()->count(), 'form [name="entry_remove_form[department]"] > option');
        $this->assertFormValue('form', 'entry_remove_form[department]', $user->getDepartments()->first()->getId(), 'User department selected');

        $crawler = $client->submitForm('Remove entry', [
            'entry_remove_form[identifier]' => '123-hest',
        ]);
        $this->assertResponseIsUnprocessable();
        $this->assertSelectorTextContains('*', 'Invalid identifier');

        $client->submitForm('Remove entry', [
            $this->getFormFieldNameByLabel('Identifier') => '123',
            // @todo Test with invalid value (error: "The selected choice is invalid")
            // 'entry_remove_form[department]' => 'xxx',
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Entry removed');
    }
}
