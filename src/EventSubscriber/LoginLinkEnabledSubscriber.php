<?php

namespace App\EventSubscriber;

use App\Settings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authenticator\LoginLinkAuthenticator;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

/**
 * Reject login link authentication when disabled by the enable_login_link setting.
 *
 * The controller guards the request and confirmation pages, but a link issued
 * while the setting was enabled would still authenticate until it expires.
 */
final readonly class LoginLinkEnabledSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Settings $settings,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => 'onCheckPassport',
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        if (!$event->getAuthenticator() instanceof LoginLinkAuthenticator) {
            return;
        }

        if (!$this->settings->get('enable_login_link')) {
            throw new AccessDeniedException('Login link authentication is disabled.');
        }
    }
}
