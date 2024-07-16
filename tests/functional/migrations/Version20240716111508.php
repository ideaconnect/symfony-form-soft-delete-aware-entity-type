<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716111508 extends AbstractMigration
{
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE deletable_entity_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE entity_with_deletable_relation_id_seq CASCADE');
        $this->addSql('ALTER TABLE entity_with_deletable_relation DROP CONSTRAINT FK_5AFC8CDEA7ED9B55');
        $this->addSql('DROP TABLE deletable_entity');
        $this->addSql('DROP TABLE entity_with_deletable_relation');
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE deletable_entity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE entity_with_deletable_relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE deletable_entity (id INT NOT NULL, name VARCHAR(128) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE entity_with_deletable_relation (id INT NOT NULL, related_deletable_entity_id INT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5AFC8CDEA7ED9B55 ON entity_with_deletable_relation (related_deletable_entity_id)');
        $this->addSql('ALTER TABLE entity_with_deletable_relation ADD CONSTRAINT FK_5AFC8CDEA7ED9B55 FOREIGN KEY (related_deletable_entity_id) REFERENCES deletable_entity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
