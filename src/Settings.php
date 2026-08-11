<?php

declare(strict_types=1);

namespace App;

use App\Repository\SettingRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

final class Settings
{
    /**
     * @var array<string, mixed>
     */
    private ?array $settings = null;

    public function __construct(
        private readonly SettingRepository $repository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function get(string $name): mixed
    {
        if (null === $this->settings) {
            $this->settings = [];
            foreach ($this->repository->findAll() as $setting) {
                $this->settings[$setting->getName()] = $setting->getValue();
            }
        }

        if (!array_key_exists($name, $this->settings)) {
            throw new \RuntimeException(sprintf('Setting "%s" does not exist. %s', $name, json_encode(array_keys($this->settings))));
        }

        return $this->settings[$name];
    }

    public function getTranslatedName(string $name): string
    {
        $t = match ($name) {
            'site_name' => t('Site name'),
            'enable_log_out' => t('Enable log out'),
            'category.user' => t('User'),
            'category.site' => t('Site'),
            'user_manual_url' => t('User manual URL'),
            'front_page_text' => t('Front page text'),
            'entry_expires_after' => t('Entry expires after'),
            'app_timezone' => t('Timezone'),
            default => throw new \RuntimeException(\sprintf('Unhandled setting name: %s', $name)),
        };

        return $t->trans($this->translator);
    }
}
