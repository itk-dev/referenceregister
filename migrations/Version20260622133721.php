<?php

declare(strict_types=1);

namespace DoctrineMigrations;

// Classes (and traits) in the DoctrineMigrations namespace are not autoloaded (cf. config/packages/doctrine_migrations.yaml)
require_once __DIR__.'/SettingTrait.php';

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622133721 extends AbstractMigration
{
    use SettingTrait;

    public function getDescription(): string
    {
        return 'Create app settings';
    }

    public function up(Schema $schema): void
    {
        $this->addSetting(
            name: 'site_name',
            description: 'Name of site',
            category: 'site',
            value: 'Referenceregister',
        );

        $this->addSetting(
            name: 'enable_log_out',
            description: 'Enable log out',
            category: 'user',
            type: Types::BOOLEAN,
            value: false,
        );

        $this->addSetting(
            name: 'max_loookups_per_day',
            description: 'Max number of lookups per day',
            category: 'user',
            type: Types::INTEGER,
            config: [
                'form_type_options' => [
                    'attr' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
            value: 5,
        );

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

        $this->addSetting(
            name: 'front_page_text',
            description: 'Front page text',
            category: 'site',
            type: Types::TEXT,
            config: [
                'form_type' => 'texteditor',
            ],
            value: '<div>Velkommen til <em>Aarhus Kommunes Referenceregister</em>.</div>',
        );

        $this->addSetting(
            name: 'entry_expires_after',
            description: 'Entry expires after',
            category: 'site',
            value: '+5 years',
        );

        $this->addSetting(
            name: 'app_timezone',
            description: 'App time zone',
            category: 'site',
            config: [
                'form_type' => 'timezone',
            ],
            value: 'Europe/Copenhagen',
        );
    }

    public function down(Schema $schema): void
    {
        $this->removeSetting(name: 'site_name');
        $this->removeSetting(name: 'enable_log_out');
        $this->removeSetting(name: 'max_loookups_per_day');
        $this->removeSetting(name: 'users_manual_url');
        $this->removeSetting(name: 'front_page_text');
        $this->removeSetting(name: 'entry_expires_after');
    }
}
