<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210212428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_contact (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, cible_id INT NOT NULL, flag_etat SMALLINT DEFAULT NULL, UNIQUE INDEX UNIQ_7C955D97953C1C61 (source_id), UNIQUE INDEX UNIQ_7C955D97A96E5E09 (cible_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_contact ADD CONSTRAINT FK_7C955D97953C1C61 FOREIGN KEY (source_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE demande_contact ADD CONSTRAINT FK_7C955D97A96E5E09 FOREIGN KEY (cible_id) REFERENCES utilisateur (id)');
        $this->addSql('DROP TABLE utilisateur_utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_utilisateur (utilisateur_source INT NOT NULL, utilisateur_target INT NOT NULL, INDEX IDX_E9FA6E2027C21577 (utilisateur_target), INDEX IDX_E9FA6E203E2745F8 (utilisateur_source), PRIMARY KEY(utilisateur_source, utilisateur_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE utilisateur_utilisateur ADD CONSTRAINT FK_E9FA6E203E2745F8 FOREIGN KEY (utilisateur_source) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_utilisateur ADD CONSTRAINT FK_E9FA6E2027C21577 FOREIGN KEY (utilisateur_target) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE demande_contact');
    }
}
