<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220323175520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, avatar VARCHAR(255) DEFAULT NULL, avatar_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1677722FFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cell (id INT AUTO_INCREMENT NOT NULL, ligne_id INT NOT NULL, flag_testee SMALLINT DEFAULT NULL, flag_presente SMALLINT DEFAULT NULL, flag_placee SMALLINT DEFAULT NULL, valeur VARCHAR(1) NOT NULL, position INT NOT NULL, INDEX IDX_CB8787E25A438E76 (ligne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT NOT NULL, flag_state SMALLINT DEFAULT NULL, INDEX IDX_A1B8AE1E953C1C61 (source_id), INDEX IDX_A1B8AE1E158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, current_player_id INT DEFAULT NULL, player_winning_id INT DEFAULT NULL, start_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', number_of_rounds INT NOT NULL, number_of_rounds_played INT DEFAULT NULL, word_to_find VARCHAR(255) NOT NULL, line_session_time INT NOT NULL, line_length INT NOT NULL, UNIQUE INDEX UNIQ_232B318C42C04473 (current_player_id), UNIQUE INDEX UNIQ_232B318C709A8442 (player_winning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invitation_to_play (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, invited_user_id INT NOT NULL, user_who_invites_id INT NOT NULL, flag_state SMALLINT DEFAULT NULL, INDEX IDX_C29AE100E48FD905 (game_id), INDEX IDX_C29AE100C58DAD6E (invited_user_id), INDEX IDX_C29AE1003C452CA4 (user_who_invites_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE line (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, game_id INT NOT NULL, flag_state SMALLINT DEFAULT NULL, remaining_seconds INT DEFAULT NULL, INDEX IDX_D114B4F699E6F5DF (player_id), INDEX IDX_D114B4F6E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_score (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, player_id INT NOT NULL, number_of_lines_found INT DEFAULT NULL, UNIQUE INDEX UNIQ_8DEB4C17E48FD905 (game_id), INDEX IDX_8DEB4C1799E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, number_of_player INT DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, INDEX IDX_C4E0A61FE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5C722232296CD8AE (team_id), INDEX IDX_5C722232A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, avatar_id INT DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, last_activity_at DATETIME DEFAULT NULL, connected TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64986CC499D (pseudo), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64986383B10 (avatar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cell ADD CONSTRAINT FK_CB8787E25A438E76 FOREIGN KEY (ligne_id) REFERENCES line (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_request ADD CONSTRAINT FK_A1B8AE1E953C1C61 FOREIGN KEY (source_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contact_request ADD CONSTRAINT FK_A1B8AE1E158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C42C04473 FOREIGN KEY (current_player_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C709A8442 FOREIGN KEY (player_winning_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invitation_to_play ADD CONSTRAINT FK_C29AE100E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE invitation_to_play ADD CONSTRAINT FK_C29AE100C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invitation_to_play ADD CONSTRAINT FK_C29AE1003C452CA4 FOREIGN KEY (user_who_invites_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE line ADD CONSTRAINT FK_D114B4F699E6F5DF FOREIGN KEY (player_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE line ADD CONSTRAINT FK_D114B4F6E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_score ADD CONSTRAINT FK_8DEB4C17E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_score ADD CONSTRAINT FK_8DEB4C1799E6F5DF FOREIGN KEY (player_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64986383B10');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE invitation_to_play DROP FOREIGN KEY FK_C29AE100E48FD905');
        $this->addSql('ALTER TABLE line DROP FOREIGN KEY FK_D114B4F6E48FD905');
        $this->addSql('ALTER TABLE player_score DROP FOREIGN KEY FK_8DEB4C17E48FD905');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
        $this->addSql('ALTER TABLE cell DROP FOREIGN KEY FK_CB8787E25A438E76');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FFB88E14F');
        $this->addSql('ALTER TABLE contact_request DROP FOREIGN KEY FK_A1B8AE1E953C1C61');
        $this->addSql('ALTER TABLE contact_request DROP FOREIGN KEY FK_A1B8AE1E158E0B66');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C42C04473');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C709A8442');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65A76ED395');
        $this->addSql('ALTER TABLE invitation_to_play DROP FOREIGN KEY FK_C29AE100C58DAD6E');
        $this->addSql('ALTER TABLE invitation_to_play DROP FOREIGN KEY FK_C29AE1003C452CA4');
        $this->addSql('ALTER TABLE line DROP FOREIGN KEY FK_D114B4F699E6F5DF');
        $this->addSql('ALTER TABLE player_score DROP FOREIGN KEY FK_8DEB4C1799E6F5DF');
        $this->addSql('ALTER TABLE team_user DROP FOREIGN KEY FK_5C722232A76ED395');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE cell');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE invitation_to_play');
        $this->addSql('DROP TABLE line');
        $this->addSql('DROP TABLE player_score');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
