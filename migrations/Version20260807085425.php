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
final class Version20260807085425 extends AbstractMigration
{
    use SettingTrait;

    public function getDescription(): string
    {
        return 'Remove Max number of lookups per day setting';
    }

    public function up(Schema $schema): void
    {
        $this->removeSetting(name: 'max_loookups_per_day');
    }

    public function down(Schema $schema): void
    {
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
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
