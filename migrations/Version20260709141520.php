<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260709141520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_department (user_id BINARY(16) NOT NULL, department_id BINARY(16) NOT NULL, INDEX IDX_6A7A2766A76ED395 (user_id), INDEX IDX_6A7A2766AE80F5DF (department_id), PRIMARY KEY (user_id, department_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_department ADD CONSTRAINT FK_6A7A2766A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_department ADD CONSTRAINT FK_6A7A2766AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_department DROP FOREIGN KEY FK_6A7A2766A76ED395');
        $this->addSql('ALTER TABLE user_department DROP FOREIGN KEY FK_6A7A2766AE80F5DF');
        $this->addSql('DROP TABLE user_department');
    }
}
