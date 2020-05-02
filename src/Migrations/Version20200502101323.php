<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200502101323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE big_game (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, INDEX IDX_441FF8F1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE big_game ADD CONSTRAINT FK_441FF8F1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD big_game_id INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C832ABAD3 FOREIGN KEY (big_game_id) REFERENCES big_game (id)');
        $this->addSql('CREATE INDEX IDX_232B318C832ABAD3 ON game (big_game_id)');
        $this->addSql('ALTER TABLE question DROP categorie_id, CHANGE blabla id_categorie INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C832ABAD3');
        $this->addSql('DROP TABLE big_game');
        $this->addSql('DROP INDEX IDX_232B318C832ABAD3 ON game');
        $this->addSql('ALTER TABLE game DROP big_game_id');
        $this->addSql('ALTER TABLE question ADD categorie_id INT NOT NULL, CHANGE id_categorie blabla INT DEFAULT NULL');
    }
}
