<?php

namespace App\Tests\E2E;

use App\Repository\UserRepository;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\Exception\InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase as BasePantherTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class PantherTestCase extends BasePantherTestCase
{
    // https://github.com/symfony/panther/issues/648
    protected static function authenticateClient(Client $client, UserInterface $user, string $firewallName = 'main'): Client
    {
        $client->request('GET', '/'.uniqid('request-to-start-session'));

        $sessionStorage = new MockFileSessionStorage(static::getContainer()->getParameter('kernel.build_dir').'/sessions');
        $session = new Session($sessionStorage);
        $token = new PostAuthenticationToken($user, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }

    protected static function getUser(string $email): UserInterface
    {
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail($email);

        if (null === $user) {
            throw new \RuntimeException(sprintf('User with email "%s" not found.', $email));
        }

        return $user;
    }

    protected function formValuesByLabel(array $values): array
    {
        $byName = [];

        foreach ($values as $label => $value) {
            $byName[$this->getFormFieldNameByLabel($label)] = $value;
        }

        return $byName;
    }

    public function getFormFieldNameByLabel(string $label): string
    {
        $crawler = static::getClient()->getCrawler();
        $labels = $crawler->filterXPath(
            \sprintf('descendant-or-self::label[contains(concat(\' \', normalize-space(string(.)), \' \'), %1$s)]', Crawler::xpathLiteral(' '.$label.' '))
        );
        if (1 !== $labels->count()) {
            throw new InvalidArgumentException(\sprintf('There is no label with "%s" as its content.', $label));
        }
        $for = $labels->attr('for');
        if ('' !== $for) {
            $fields = $crawler->filter('[id="'.$for.'"]');
            if ($fields->count() > 0) {
                return $fields->attr('name');
            }
        }

        throw new InvalidArgumentException(\sprintf('There is no field with ID "%s" for label "%s"', $for, $label));
    }

    // Lifted from https://github.com/coccoinomane/phpunit-log
    protected static function print(mixed $message): void
    {
        $stream = STDERR;

        if (is_array($message) || is_object($message)) {
            fwrite($stream, print_r($message, true));
        } else {
            fwrite($stream, $message);
        }

        fwrite($stream, PHP_EOL);
    }
}
