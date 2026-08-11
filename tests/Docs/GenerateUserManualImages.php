<?php

namespace App\Tests\Docs;

use App\Tests\E2E\PantherTestCase;
use PHPUnit\Framework\Attributes\Depends;
use Symfony\Component\Panther\Client;
use Symfony\Component\Translation\LocaleSwitcher;

final class GenerateUserManualImages extends PantherTestCase
{
    private ?string $context = null;
    private LocaleSwitcher $localeSwitcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = null;
        $this->localeSwitcher = static::getContainer()->get(LocaleSwitcher::class);
    }

    public function testGenerateLoginImages(): void
    {
        // The value 515 is computed by trial and error …
        $client = $this->createTheClient(windowHeight: 515);
        $this->context = 'login';

        $client->get('/');
        // For some reason the browser shows to middle of the page.
        $client->executeScript('scrollTo(0, 0)');
        $this->takeScreenshot('oidc-predefined-users.png');
    }

    public function testGenerateManagerImages(): void
    {
        $client = static::authenticateClient(
            static::createTheClient(),
            $this->getUser('manager@department1.example.com'),
        );
        $this->context = 'manager';

        // Add

        $client->request('GET', '/');
        $this->takeScreenshot('front-page.png');

        $client->request('GET', '/add');
        $this->takeScreenshot('entry-add.png');

        $buttonNode = $client->getCrawler()->selectButton('entry_add_form[submit]');
        $form = $buttonNode->form([
            'entry_add_form[identifier]' => 'Test 123',
        ]);
        $this->takeScreenshot('entry-add-filled-invalid.png');
        $client->submit($form);
        $this->takeScreenshot('entry-add-invalid-identifier.png');

        $client->request('GET', '/add');
        $buttonNode = $client->getCrawler()->selectButton('entry_add_form[submit]');
        $form = $buttonNode->form([
            'entry_add_form[identifier]' => 'test-123',
        ]);
        $this->takeScreenshot('entry-add-filled.png');
        $client->submit($form);

        $this->takeScreenshot('entry-add-succes.png');

        // Look up

        $client->request('GET', '/look-up');
        $this->takeScreenshot('entry-look-up.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test',
        ]);
        $this->takeScreenshot('entry-look-up-filled-no-match.png');
        $client->submit($form);
        $this->takeScreenshot('entry-look-up-no-match.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test-123',
        ]);
        $this->takeScreenshot('entry-look-up-filled.png');
        $client->submit($form);

        $this->takeScreenshot('entry-look-up-match.png');

        // remove

        $client->request('GET', '/remove');
        $this->takeScreenshot('entry-remove.png');

        $client->request('GET', '/remove');
        $buttonNode = $client->getCrawler()->selectButton('entry_remove_form[submit]');
        $form = $buttonNode->form([
            'entry_remove_form[identifier]' => 'test',
        ]);
        $this->takeScreenshot('entry-remove-filled.png');
        $client->submit($form);
        $this->takeScreenshot('entry-removed.png');
    }

    #[Depends('testGenerateManagerImages')]
    public function testGenerateUserImages(): void
    {
        $client = static::authenticateClient(
            static::createTheClient(),
            $this->getUser('user@department1.example.com'),
        );
        $this->context = 'user';

        $client->request('GET', '/');
        $this->takeScreenshot('front-page.png');

        // Look up
        $client->request('GET', '/look-up');
        $this->takeScreenshot('entry-look-up.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test',
        ]);
        $this->takeScreenshot('entry-look-up-filled-no-match.png');
        $client->submit($form);
        $this->takeScreenshot('entry-look-up-no-match.png');

        $client->request('GET', '/look-up');
        $buttonNode = $client->getCrawler()->selectButton('entry_look_up_form[submit]');
        $form = $buttonNode->form([
            'entry_look_up_form[identifier]' => 'test-123',
        ]);
        $this->takeScreenshot('entry-look-up-filled.png');
        $client->submit($form);

        $this->takeScreenshot('entry-look-up-match.png');

        $client->request('GET', '/look-up');
        $this->takeScreenshot('entry-look-up-limit-reached.png');
    }

    private array $imagePaths = [];

    private function takeScreenshot(string $name): void
    {
        if (null === $this->context) {
            throw new \RuntimeException('No image context set');
        }

        $imagePath = sprintf('docs/user-manual/%s/images/%s/%s', $this->localeSwitcher->getLocale(), $this->context, $name);
        if (array_key_exists($imagePath, $this->imagePaths)) {
            throw new \RuntimeException(sprintf('Duplicate image path "%s"', $imagePath));
        }
        $this->imagePaths[$imagePath] = $imagePath;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        static::getClient()->takeScreenshot($imagePath);
        $this->assertFileExists($imagePath);
        $this->print(sprintf('Image written to %s', $imagePath));
    }

    protected static function createTheClient(int $windowHeight = 768): Client
    {
        $windowSize = [
            1024,
            $windowHeight,
        ];

        return static::createPantherClient(
            options: [
                'browser' => static::CHROME,
                'browser_arguments' => [
                    '--disable-dev-shm-usage',
                    sprintf('--window-size=%d,%d', $windowSize[0], $windowSize[1]),
                    // --window-size (apparently) works only when --headless is also specified.
                    '--headless',
                    '--no-sandbox',
                    '--hide-scrollbars',
                ],
            ],
        );
    }
}
