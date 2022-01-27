<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220127202742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cellule (id INT AUTO_INCREMENT NOT NULL, ligne_id INT NOT NULL, flag_testee SMALLINT DEFAULT NULL, flag_presente SMALLINT DEFAULT NULL, flag_placee SMALLINT DEFAULT NULL, valeur VARCHAR(1) NOT NULL, position INT NOT NULL, INDEX IDX_F493DEDF5A438E76 (ligne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne (id INT AUTO_INCREMENT NOT NULL, id_joueur_id INT NOT NULL, partie_id INT NOT NULL, flag_etat SMALLINT DEFAULT NULL, secondes_restantes INT NOT NULL, INDEX IDX_57F0DB8329D76B4B (id_joueur_id), INDEX IDX_57F0DB83E075F7A4 (partie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, id_joueur_actuel_id INT DEFAULT NULL, id_joueur_gagnant_id INT DEFAULT NULL, date_debut DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', nombre_tours INT NOT NULL, nombre_tours_joues INT DEFAULT NULL, mot_a_trouver VARCHAR(255) NOT NULL, duree_session_ligne INT NOT NULL, longueur_lignes INT NOT NULL, UNIQUE INDEX UNIQ_59B1F3D144B2D76 (id_joueur_actuel_id), UNIQUE INDEX UNIQ_59B1F3D9680ADF3 (id_joueur_gagnant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie_utilisateur (partie_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_87F5E0FBE075F7A4 (partie_id), INDEX IDX_87F5E0FBFB88E14F (utilisateur_id), PRIMARY KEY(partie_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE score_joueur (id INT AUTO_INCREMENT NOT NULL, id_partie_id INT NOT NULL, id_joueur_id INT NOT NULL, nombre_lignes_trouvees INT DEFAULT NULL, UNIQUE INDEX UNIQ_1EABEE1B60404B83 (id_partie_id), INDEX IDX_1EABEE1B29D76B4B (id_joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cellule ADD CONSTRAINT FK_F493DEDF5A438E76 FOREIGN KEY (ligne_id) REFERENCES ligne (id)');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB8329D76B4B FOREIGN KEY (id_joueur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB83E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D144B2D76 FOREIGN KEY (id_joueur_actuel_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D9680ADF3 FOREIGN KEY (id_joueur_gagnant_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE partie_utilisateur ADD CONSTRAINT FK_87F5E0FBE075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partie_utilisateur ADD CONSTRAINT FK_87F5E0FBFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE score_joueur ADD CONSTRAINT FK_1EABEE1B60404B83 FOREIGN KEY (id_partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE score_joueur ADD CONSTRAINT FK_1EABEE1B29D76B4B FOREIGN KEY (id_joueur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cellule DROP FOREIGN KEY FK_F493DEDF5A438E76');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB83E075F7A4');
        $this->addSql('ALTER TABLE partie_utilisateur DROP FOREIGN KEY FK_87F5E0FBE075F7A4');
        $this->addSql('ALTER TABLE score_joueur DROP FOREIGN KEY FK_1EABEE1B60404B83');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB8329D76B4B');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D144B2D76');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D9680ADF3');
        $this->addSql('ALTER TABLE partie_utilisateur DROP FOREIGN KEY FK_87F5E0FBFB88E14F');
        $this->addSql('ALTER TABLE score_joueur DROP FOREIGN KEY FK_1EABEE1B29D76B4B');
        $this->addSql('DROP TABLE cellule');
        $this->addSql('DROP TABLE ligne');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE partie_utilisateur');
        $this->addSql('DROP TABLE score_joueur');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
