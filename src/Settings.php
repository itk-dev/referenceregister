<?php

namespace App;

use App\Repository\SettingRepository;

final class Settings
{
    /**
     * @var array<string, mixed>
     */
    private ?array $settings = null;

    public function __construct(
        private readonly SettingRepository $repository,
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
            throw new \RuntimeException(sprintf('Setting "%s" does not exist.', $name));
        }

        return $this->settings[$name];
    }
}
