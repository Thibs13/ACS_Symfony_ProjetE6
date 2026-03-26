<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326175144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise CHANGE ent_telephone ent_telephone VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60A2BCE098 FOREIGN KEY (vil_id_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3E7CA2E9 FOREIGN KEY (fil_id_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC2EB7D700 FOREIGN KEY (uti_id_id) REFERENCES utilisateur (UTI_ID)');
        $this->addSql('ALTER TABLE stage ADD CONSTRAINT FK_C27C9369220963CB FOREIGN KEY (etu_id_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE stage ADD CONSTRAINT FK_C27C93696905459C FOREIGN KEY (ent_id_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE stage ADD CONSTRAINT FK_C27C936919625E0A FOREIGN KEY (enseignant_visite_id) REFERENCES utilisateur (UTI_ID)');
        $this->addSql('ALTER TABLE stage ADD CONSTRAINT FK_C27C9369D543D068 FOREIGN KEY (enseignant_suivi_id) REFERENCES utilisateur (UTI_ID)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3CC0F380A FOREIGN KEY (ROL_ID) REFERENCES role (ROL_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60A2BCE098');
        $this->addSql('ALTER TABLE entreprise CHANGE ent_telephone ent_telephone INT NOT NULL');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3E7CA2E9');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC2EB7D700');
        $this->addSql('ALTER TABLE stage DROP FOREIGN KEY FK_C27C9369220963CB');
        $this->addSql('ALTER TABLE stage DROP FOREIGN KEY FK_C27C93696905459C');
        $this->addSql('ALTER TABLE stage DROP FOREIGN KEY FK_C27C936919625E0A');
        $this->addSql('ALTER TABLE stage DROP FOREIGN KEY FK_C27C9369D543D068');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3CC0F380A');
    }
}
