<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Department;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ItkDev\OpenIdConnect\Exception\ClaimsException;
use ItkDev\OpenIdConnect\Exception\OpenIdConnectExceptionInterface;
use ItkDev\OpenIdConnectBundle\Security\OpenIdConfigurationProviderManager;
use ItkDev\OpenIdConnectBundle\Security\OpenIdLoginAuthenticator;
use Psr\Log\LoggerInterface;
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
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct($providerManager);
    }

    public function authenticate(Request $request): Passport
    {
        try {
            $claims = $this->validateClaims($request);
            $email = trim((string) ($claims['email'] ?? $claims['upn'] ?? null));
            if ('' === $email) {
                throw new ClaimsException('Cannot get email from claims');
            }
            $rolesClaim = (string) ($this->options['roles_claim'] ?? 'roles');
            $roles = (array) ($claims[$rolesClaim] ?? []);
            $departmentClaim = (string) ($this->options['departments_claim'] ?? 'department');
            $departmentNames = (array) ($claims[$departmentClaim] ?? []);

            // Check if user exists already - if not create a user
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if (null === $user) {
                // Create the new user and persist it
                $user = new User();
                $this->entityManager->persist($user);
            }

            $user->setEmail($email);

            /** @var list<string> $userRoles */
            $userRoles = [];
            if (array_key_exists('role_map', $this->options) && is_array($this->options['role_map'])) {
                $map = $this->options['role_map'];
                // @mago-ignore analysis:invalid-type-cast
                /** @var array<array<string>> $mappedUserRoles */
                $mappedUserRoles = array_map(static fn (string $role) => (array) ($map[$role] ?? null), $roles);
                // Flatten and filter out invalid roles.
                $userRoles =
                    array_filter(
                        array_merge(...$mappedUserRoles),
                        static fn (string $role) => null !== Role::tryFrom($role),
                    )
                    |> array_values(...);
            }

            $user->setRoles($userRoles);

            // Map department names. If a name is not mapped, we just keep the name as it is.
            if (array_key_exists('department_map', $this->options) && is_array($this->options['department_map'])) {
                $map = $this->options['department_map'];
                // @mago-ignore analysis:invalid-type-cast
                $departmentNames = array_map(
                    static fn (string $name) => (array) ($map[$name] ?? $name),
                    $departmentNames,
                );
            }
            foreach ($user->getDepartments() as $department) {
                $user->removeDepartment($department);
            }
            $departments = $this->entityManager->getRepository(Department::class)->findBy(['name' => $departmentNames]);
            foreach ($departments as $department) {
                $user->addDepartment($department);
            }

            $this->entityManager->flush();

            return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        } catch (OpenIdConnectExceptionInterface $exception) {
            // Authentication failed
            $this->logException($exception);

            throw new CustomUserMessageAuthenticationException($exception->getMessage());
        }
    }

    public function onAuthenticationSuccess(
        Request $request,
        #[\SensitiveParameter]
        TokenInterface $token,
        string $firewallName,
    ): ?Response {
        return new RedirectResponse($this->router->generate('app_default'));
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('itkdev_openid_connect_login', [
            'providerKey' => 'user',
        ]));
    }

    /**
     * @template T of \Throwable
     *
     * @param T $exception
     *
     * @return T
     */
    private function logException(\Throwable $exception): \Throwable
    {
        $this->logger->error('exception: {message}', [
            'message' => $exception->getMessage(),
            'exception' => $exception,
        ]);

        return $exception;
    }
}
