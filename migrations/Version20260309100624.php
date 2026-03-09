<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309100624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD UTI_Nom VARCHAR(38) NOT NULL, ADD UTI_Prenom VARCHAR(38) NOT NULL, ADD UTI_Login VARCHAR(38) NOT NULL, DROP nom, DROP prenom, DROP login, CHANGE id UTI_ID INT AUTO_INCREMENT NOT NULL, CHANGE password UTI_Password VARCHAR(255) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (UTI_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur MODIFY UTI_ID INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD nom VARCHAR(38) NOT NULL, ADD prenom VARCHAR(38) NOT NULL, ADD login VARCHAR(38) NOT NULL, DROP UTI_Nom, DROP UTI_Prenom, DROP UTI_Login, CHANGE UTI_ID id INT AUTO_INCREMENT NOT NULL, CHANGE UTI_Password password VARCHAR(255) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
