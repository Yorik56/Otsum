<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220411023930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE in_game_player_status ADD related_game_id INT NOT NULL');
        $this->addSql('ALTER TABLE in_game_player_status ADD CONSTRAINT FK_9AD37D38DB9613A0 FOREIGN KEY (related_game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_9AD37D38DB9613A0 ON in_game_player_status (related_game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE in_game_player_status DROP FOREIGN KEY FK_9AD37D38DB9613A0');
        $this->addSql('DROP INDEX IDX_9AD37D38DB9613A0 ON in_game_player_status');
        $this->addSql('ALTER TABLE in_game_player_status DROP related_game_id');
    }
}
