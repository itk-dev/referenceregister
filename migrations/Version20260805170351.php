<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// Classes (and traits) in the DoctrineMigrations namespace are not autoloaded (cf. config/packages/doctrine_migrations.yaml)
require_once __DIR__.'/SettingTrait.php';

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805170351 extends AbstractMigration
{
    use SettingTrait;

    public function getDescription(): string
    {
        return 'Update user manual settings';
    }

    public function up(Schema $schema): void
    {
        // Note that we deliberately overwrite any current user's manual URL.
        $this->addSetting(
            name: 'user_manual_url',
            description: 'User manual URL',
            category: 'user',
            config: [
                'form_type_options' => [
                    'required' => false,
                ],
            ],
            // The trailing slash is important!
            value: '/user-manual/da/bruger/',
        );
        $this->removeSetting(name: 'users_manual_url');
    }

    public function down(Schema $schema): void
    {
        $this->addSetting(
            name: 'users_manual_url',
            description: "User's manual URL",
            category: 'user',
            config: [
                'form_type' => 'url',
                'form_type_options' => [
                    'required' => false,
                ],
            ],
        );
        $this->removeSetting(name: 'user_manual_url');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
