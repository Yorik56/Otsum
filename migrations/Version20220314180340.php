<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220314180340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, nbr_joueur INT DEFAULT NULL, couleur VARCHAR(255) DEFAULT NULL, INDEX IDX_C4E0A61FE075F7A4 (partie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_utilisateur (team_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_F62C8CFE296CD8AE (team_id), INDEX IDX_F62C8CFEFB88E14F (utilisateur_id), PRIMARY KEY(team_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE team_utilisateur ADD CONSTRAINT FK_F62C8CFE296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_utilisateur ADD CONSTRAINT FK_F62C8CFEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_utilisateur DROP FOREIGN KEY FK_F62C8CFE296CD8AE');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_utilisateur');
    }
}
