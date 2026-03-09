<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309110011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role (ROL_ID INT AUTO_INCREMENT NOT NULL, ROL_Libelle VARCHAR(38) NOT NULL, PRIMARY KEY (ROL_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (UTI_ID INT AUTO_INCREMENT NOT NULL, UTI_Nom VARCHAR(38) NOT NULL, UTI_Prenom VARCHAR(38) NOT NULL, UTI_Login VARCHAR(38) NOT NULL, UTI_Password VARCHAR(255) NOT NULL, ROL_ID INT NOT NULL, INDEX IDX_1D1C63B3CC0F380A (ROL_ID), PRIMARY KEY (UTI_ID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3CC0F380A FOREIGN KEY (ROL_ID) REFERENCES role (ROL_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3CC0F380A');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE utilisateur');
    }
}
