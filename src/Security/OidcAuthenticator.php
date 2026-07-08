<?php

namespace App\Security;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ItkDev\OpenIdConnect\Exception\OpenIdConnectExceptionInterface;
use ItkDev\OpenIdConnectBundle\Security\OpenIdConfigurationProviderManager;
use ItkDev\OpenIdConnectBundle\Security\OpenIdLoginAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OidcAuthenticator extends OpenIdLoginAuthenticator
{
    /**
     * @param array<array-key, mixed> $options
     */
    public function __construct(
        OpenIdConfigurationProviderManager $providerManager,
        private readonly UrlGeneratorInterface $router,
        private readonly EntityManagerInterface $entityManager,
        private readonly array $options,
    ) {
        parent::__construct($providerManager);
    }

    public function authenticate(Request $request): Passport
    {
        try {
            /**
             * @var array{
             *     upn: string,
             *     email?: string,
             *     roles: string[],
             * } $claims
             */
            $claims = $this->validateClaims($request);
            $email = $claims['email'] ?? $claims['upn'];
            $rolesClaim = $this->options['roles_claim'] ?? 'roles';
            $roles = $claims[$rolesClaim] ?? [];

            // Check if user exists already - if not create a user
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $email]);
            if (null === $user) {
                // Create the new user and persist it
                $user = new User();
                $this->entityManager->persist($user);
            }

            if (is_array($roles)) {
                $map = (array) ($this->options['role_map'] ?? null);
                $userRoles = array_map(static fn (string $role) => (array) ($map[$role] ?? null), $roles);
                // Flatten and filter out invalid roles.
                $userRoles = array_filter(array_merge(...$userRoles), static fn (string $role) => null !== Role::tryFrom($role));
                $user->setRoles($userRoles);
            }
            $user->setEmail($email);

            $this->entityManager->flush();

            return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        } catch (OpenIdConnectExceptionInterface $exception) {
            // Authentication failed
            throw new CustomUserMessageAuthenticationException($exception->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_default'));
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('itkdev_openid_connect_login', [
            'providerKey' => 'user',
        ]));
    }
}
