<?php

namespace App\Tests\Application;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Exception\InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class AppTest extends WebTestCase
{
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
            'entry_look_up_form[identifier]' => '123-test',
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
            'entry_remove_form[identifier]' => '123-test',
        ]);
        $this->assertResponseIsUnprocessable();
        $this->assertSelectorTextContains('*', 'Invalid identifier');

        $client->submitForm('Remove entry', [
            // 'entry_remove_form[identifier]' => '123',
            $this->getFormFieldNameByLabel($crawler, 'Identifier') => '123',
            // @todo Test with invalid value (error: "The selected choice is invalid")
            // 'entry_remove_form[department]' => 'xxx',
        ]);
        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Entry removed');
    }

    public function getFormFieldNameByLabel(Crawler $crawler, string $value): string
    {
        $labels = $crawler->filterXPath(
            \sprintf('descendant-or-self::label[contains(concat(\' \', normalize-space(string(.)), \' \'), %1$s)]', Crawler::xpathLiteral(' '.$value.' '))
        );
        if (1 !== $labels->count()) {
            throw new InvalidArgumentException(\sprintf('There is no label with "%s" as its content.', $value));
        }
        $for = $labels->attr('for');
        if ('' !== $for) {
            $fields = $crawler->filter('[id="'.$for.'"]');
            if ($fields->count() > 0) {
                return $fields->attr('name');
            }
        }

        throw new InvalidArgumentException(\sprintf('There is no field with ID "%s" for label "%s"', $for, $value));
    }
}
