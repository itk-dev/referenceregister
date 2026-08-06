<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20260805143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create enable_login_link setting';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO `setting` (`name`, `description`, `category`, `type`, `config`, `value`) VALUES (:name, :description, :category, :type, :config, :value)', [
            'name' => 'enable_login_link',
            'description' => 'Enable login link',
            'category' => 'user',
            'type' => Types::BOOLEAN,
            'config' => json_encode([]),
            'value' => json_encode(false),
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM `setting` WHERE `name` = :name', ['name' => 'enable_login_link']);
    }
}
