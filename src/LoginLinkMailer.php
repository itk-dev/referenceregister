<?php

namespace App;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

class LoginLinkMailer
{
    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly Settings $settings,
        // The login link lifetime in seconds (cf. config/services.yaml).
        private readonly int $lifetime,
    ) {
    }

    public function sendLoginLink(User $user): void
    {
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);
        $siteName = (string) $this->settings->get('site_name');

        $email = new TemplatedEmail()
            ->to(new Address((string) $user->getEmail()))
            ->subject(t('Log in to {site_name}', ['site_name' => $siteName])->trans($this->translator))
            ->htmlTemplate('emails/login_link.html.twig')
            ->textTemplate('emails/login_link.txt.twig')
            ->context([
                'login_link_details' => $loginLinkDetails,
                'site_name' => $siteName,
                // @todo: How do we display this nicely if lifetime is configured to less than 60 seconds?
                'expires_in_minutes' => max(1, (int) floor($this->lifetime / 60)),
            ]);

        $this->mailer->send($email);
    }
}
