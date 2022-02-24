<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220223231933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB8329D76B4B');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB8329D76B4B FOREIGN KEY (id_joueur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB8329D76B4B');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB8329D76B4B FOREIGN KEY (id_joueur_id) REFERENCES utilisateur (id)');
    }
}
