<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622133721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create app settings';
    }

    public function up(Schema $schema): void
    {
        $addSetting = fn (array $parameters) => $this->addSql('INSERT INTO `setting` (`name`, `description`, `category`, `type`, `config`, `value`) VALUES (:name, :description, :category, :type, :config, :value)', $parameters);

        $addSetting([
            'name' => 'site_name',
            'description' => 'Name of site',
            'category' => 'site',
            'type' => Types::STRING,
            'config' => json_encode([]),
            'value' => json_encode('Referenceregister'),
        ]);

        $addSetting([
            'name' => 'enable_log_out',
            'description' => 'Enable log out',
            'category' => 'user',
            'type' => Types::BOOLEAN,
            'config' => json_encode([]),
            'value' => json_encode(false),
        ]);

        $addSetting([
            'name' => 'max_loookups_per_day',
            'description' => 'Max number of lookups per day',
            'category' => 'user',
            'type' => Types::INTEGER,
            'config' => json_encode([
                'form_type_options' => [
                    'attr' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ]),
            'value' => json_encode(5),
        ]);

        $addSetting([
            'name' => 'users_manual_url',
            'description' => "User's manual URL",
            'category' => 'user',
            'type' => Types::STRING,
            'config' => json_encode([
                'form_type' => 'url',
                'form_type_options' => [
                    'required' => false,
                ],
            ]),
            'value' => json_encode(null),
        ]);

        $addSetting([
            'name' => 'front_page_text',
            'description' => 'Front page text',
            'category' => 'site',
            'type' => Types::TEXT,
            'config' => json_encode([
                'form_type' => 'texteditor',
            ]),
            'value' => json_encode('<div>Velkommen til <em>Aarhus Kommunes Referenceregister</em>.</div>'),
        ]);

        $addSetting([
            'name' => 'entry_expires_after',
            'description' => 'Entry expires after',
            'category' => 'site',
            'type' => Types::STRING,
            'config' => json_encode([]),
            'value' => json_encode('+5 years'),
        ]);

        $timeZones = \DateTimeZone::listIdentifiers();
        $addSetting([
            'name' => 'app_timezone',
            'description' => 'App time zone',
            'category' => 'site',
            'type' => Types::STRING,
            'config' => json_encode([
                'form_type' => 'choice',
                'form_type_options' => [
                    'choices' => array_combine($timeZones, $timeZones),
                ],
            ]),
            'value' => json_encode('Europe/Copenhagen'),
        ]);
    }

    public function down(Schema $schema): void
    {
        $removeSetting = fn (string $name) => $this->addSql('DELETE FROM `setting` WHERE `name` = :name', ['name' => $name]);

        $removeSetting('site_name');
        $removeSetting('enable_log_out');
        $removeSetting('max_loookups_per_day');
        $removeSetting('users_manual_url');
        $removeSetting('front_page_text');
        $removeSetting('entry_expires_after');
    }
}
