<?php

namespace App\Controller;

use App\Form\LoginLinkRequestFormType;
use App\LoginLinkMailer;
use App\Repository\UserRepository;
use App\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginLinkController extends AbstractController
{
    public function __construct(
        private readonly Settings $settings,
        private readonly UserRepository $userRepository,
        private readonly LoginLinkMailer $loginLinkMailer,
    ) {
    }

    #[Route('/login/link', name: 'app_login_link_request', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function request(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $this->denyAccessUnlessLoginLinkEnabled();

        $form = $this->createForm(LoginLinkRequestFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();
            $user = $this->userRepository->findOneByEmail($email);
            // Do not reveal whether the email address is registered.
            if (null !== $user) {
                $this->loginLinkMailer->sendLoginLink($user);
            }

            return $this->redirectToRoute('app_login_link_sent');
        }

        return $this->render('login_link/request.html.twig', [
            'form' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/login/link/sent', name: 'app_login_link_sent', methods: [Request::METHOD_GET])]
    public function sent(): Response
    {
        $this->denyAccessUnlessLoginLinkEnabled();

        return $this->render('login_link/sent.html.twig');
    }

    /**
     * Render an intermediate page asking the user to confirm the login.
     */
    #[Route('/login/link/check', name: 'app_login_link_check', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function check(Request $request): Response
    {
        $this->denyAccessUnlessLoginLinkEnabled();

        return $this->render('login_link/check.html.twig', [
            'expires' => $request->query->get('expires'),
            'user' => $request->query->get('user'),
            'hash' => $request->query->get('hash'),
        ]);
    }

    private function denyAccessUnlessLoginLinkEnabled(): void
    {
        if (!$this->settings->get('enable_login_link')) {
            throw $this->createNotFoundException();
        }
    }
}
