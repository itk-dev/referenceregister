<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260708142233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_person (id BINARY(16) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, department_id BINARY(16) NOT NULL, INDEX IDX_A44EE6F7AE80F5DF (department_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE department (id BINARY(16) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE entry (id BINARY(16) NOT NULL, hash VARCHAR(255) NOT NULL, expired_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, department_id BINARY(16) NOT NULL, INDEX IDX_2B219D70AE80F5DF (department_id), INDEX entry_hash_idx (hash), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contact_person ADD CONSTRAINT FK_A44EE6F7AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE setting CHANGE value value JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_person DROP FOREIGN KEY FK_A44EE6F7AE80F5DF');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70AE80F5DF');
        $this->addSql('DROP TABLE contact_person');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE setting CHANGE value value JSON DEFAULT NULL');
    }
}
