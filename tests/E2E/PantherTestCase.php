<?php

namespace App\Tests\E2E;

use App\Tests\ApplicationTestCaseTrait;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase as BasePantherTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class PantherTestCase extends BasePantherTestCase
{
    use ApplicationTestCaseTrait;

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
}
