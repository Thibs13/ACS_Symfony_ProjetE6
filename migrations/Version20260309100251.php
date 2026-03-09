<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309100251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE role CHANGE id ROL_ID INT AUTO_INCREMENT NOT NULL, CHANGE libelle ROL_Libelle VARCHAR(38) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (ROL_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role MODIFY ROL_ID INT NOT NULL');
        $this->addSql('ALTER TABLE role CHANGE ROL_ID id INT AUTO_INCREMENT NOT NULL, CHANGE ROL_Libelle libelle VARCHAR(38) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
