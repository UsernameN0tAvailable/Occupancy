<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190820082840 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, max_occupancy SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE minute_entry ADD location_id INT NOT NULL, CHANGE timestamp date_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE minute_entry ADD CONSTRAINT FK_72E49D0A64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_72E49D0A64D218E ON minute_entry (location_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE minute_entry DROP FOREIGN KEY FK_72E49D0A64D218E');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP INDEX IDX_72E49D0A64D218E ON minute_entry');
        $this->addSql('ALTER TABLE minute_entry DROP location_id, CHANGE date_time timestamp DATETIME NOT NULL');
    }
}
