<?php

namespace App\Tests\Docs;

use App\Tests\E2E\PantherTestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Translation\LocaleSwitcher;

final class GenerateDocsImages extends PantherTestCase
{
    private ?string $context = null;
    private int $imageCount = 0;
    private LocaleSwitcher $localeSwitcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = null;
        $this->imageCount = 0;
        $this->localeSwitcher = static::getContainer()->get(LocaleSwitcher::class);
    }

    public function testGenerateManagerImages(): void
    {
        $client = static::authenticateClient(
            static::createPantherClient(),
            $this->getUser('manager@department1.example.com'),
        );
        $this->context = 'manager';

        // Add

        $client->request('GET', '/');
        $this->takeScreenshot($client, 'front-page.png');

        $client->request('GET', '/add');
        $this->takeScreenshot($client, 'entry-add.png');

        $buttonNode = $client->getCrawler()->selectButton('entry_add_form[submit]');
        $form = $buttonNode->form([
            'entry_add_form[identifier]' => 'xxx',
        ]);
        $this->takeScreenshot($client, 'entry-add-filled-invalid.png');
        $client->submit($form);
        $this->takeScreenshot($client, 'entry-add-invalid-identifier.png');

        $client->request('GET', '/add');
        $buttonNode = $client->getCrawler()->selectButton('entry_add_form[submit]');
        $form = $buttonNode->form([
            'entry_add_form[identifier]' => 'test-123',
        ]);
        $this->takeScreenshot($client, 'entry-add-filled.png');
        $client->submit($form);

        $this->takeScreenshot($client, 'entry-add-succes.png');

        // Look up

        $client->request('GET', '/look-up');
        $this->takeScreenshot($client, 'entry-look-up.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test',
        ]);
        $this->takeScreenshot($client, 'entry-look-up-filled-no-match.png');
        $client->submit($form);
        $this->takeScreenshot($client, 'entry-look-up-no-match.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test-123',
        ]);
        $this->takeScreenshot($client, 'entry-look-up-filled.png');
        $client->submit($form);

        $this->takeScreenshot($client, 'entry-look-up-match.png');

        // remove

        $client->request('GET', '/remove');
        $this->takeScreenshot($client, 'entry-remove.png');

        $client->request('GET', '/remove');
        $buttonNode = $client->getCrawler()->selectButton('entry_remove_form[submit]');
        $form = $buttonNode->form([
            'entry_remove_form[identifier]' => 'test',
        ]);
        $this->takeScreenshot($client, 'entry-remove-filled.png');
        $client->submit($form);
        $this->takeScreenshot($client, 'entry-removed.png');
    }

    public function testGenerateUserImages(): void
    {
        $client = static::authenticateClient(
            static::createPantherClient(),
            $this->getUser('user@department1.example.com'),
        );
        $this->context = 'user';

        // Add
        $client->request('GET', '/');
        $this->takeScreenshot($client, 'front-page.png');

        // Look up
        $client->request('GET', '/look-up');
        $this->takeScreenshot($client, 'entry-look-up.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test',
        ]);
        $this->takeScreenshot($client, 'entry-look-up-filled-no-match.png');
        $client->submit($form);
        $this->takeScreenshot($client, 'entry-look-up-no-match.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test-123',
        ]);
        $this->takeScreenshot($client, 'entry-look-up-filled.png');
        $client->submit($form);

        $this->takeScreenshot($client, 'entry-look-up-match.png');
    }

    private function takeScreenshot(Client $client, string $name): void
    {
        if (null === $this->context) {
            throw new \RuntimeException('No image context set');
        }

        $imagePath = sprintf('docs/%s/images/%s/%03d-%s', $this->localeSwitcher->getLocale(), $this->context, $this->imageCount++, $name);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $client->takeScreenshot($imagePath);
        $this->assertFileExists($imagePath);
        $this->print(sprintf('Image written to %s', $imagePath));
    }
}
