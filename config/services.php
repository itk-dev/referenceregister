<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\EntryManager;
use App\Security\OidcAuthenticator;
use App\Tests\Mock\TestEntryManager;

return App::config([
    'services' => [
        // Autowiring and autoconfiguration are enabled by default when using App::config()
        // '_defaults' => [
        //     'autowire' => true,      // Automatically injects dependencies in your services.
        //     'autoconfigure' => true, // Automatically registers your services as commands, event subscribers, etc.
        // ],
        'App\\' => [
            'resource' => '../src/',
        ],
        // order is important in this file because service definitions
        // always *replace* previous ones; add your own service configuration below

        OidcAuthenticator::class => [
            'arguments' => [
                '$options' => [
                    'roles_claim' => '%env(OIDC_ROLES_CLAIM)%',
                    'role_map' => '%env(json:OIDC_ROLE_MAP)%',
                    'departments_claim' => '%env(OIDC_DEPARTMENTS_CLAIM)%',
                    'department_map' => '%env(json:OIDC_DEPARTMENT_MAP)%',
                ],
            ],
        ],

        EntryManager::class => [
            'class' => ($_ENV['USE_TEST_ENTRY_MANAGER'] ?? false) ? TestEntryManager::class : EntryManager::class,
        ],
    ],
]);
