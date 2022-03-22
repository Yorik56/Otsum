<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220320000252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitation_to_play (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, invited_user_id INT NOT NULL, user_who_invites_id INT NOT NULL, flag_etat SMALLINT DEFAULT NULL, INDEX IDX_C29AE100E075F7A4 (partie_id), INDEX IDX_C29AE100C58DAD6E (invited_user_id), INDEX IDX_C29AE1003C452CA4 (user_who_invites_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitation_to_play ADD CONSTRAINT FK_C29AE100E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE invitation_to_play ADD CONSTRAINT FK_C29AE100C58DAD6E FOREIGN KEY (invited_user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE invitation_to_play ADD CONSTRAINT FK_C29AE1003C452CA4 FOREIGN KEY (user_who_invites_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invitation_to_play');
    }
}
