<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260309103328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD FK_ROL_ID INT NOT NULL, DROP role_id');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B39726B06F FOREIGN KEY (FK_ROL_ID) REFERENCES role (ROL_ID)');
        $this->addSql('CREATE INDEX IDX_1D1C63B39726B06F ON utilisateur (FK_ROL_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B39726B06F');
        $this->addSql('DROP INDEX IDX_1D1C63B39726B06F ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur ADD role_id INT DEFAULT NULL, DROP FK_ROL_ID');
    }
}
