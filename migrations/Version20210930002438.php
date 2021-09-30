<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210930002438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, localite_id INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, quantite INT DEFAULT NULL, montant INT DEFAULT NULL, id_transaction VARCHAR(255) DEFAULT NULL, status_transaction VARCHAR(255) DEFAULT NULL, tel_transaction VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, date_transaction VARCHAR(255) DEFAULT NULL, time_transaction VARCHAR(255) DEFAULT NULL, INDEX IDX_6EEAA67D1137ABCF (album_id), INDEX IDX_6EEAA67D924DD2B5 (localite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D924DD2B5 FOREIGN KEY (localite_id) REFERENCES localite (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE commande');
    }
}
