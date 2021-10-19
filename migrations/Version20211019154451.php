<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019154451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE encaissement DROP FOREIGN KEY FK_5D4869B01137ABCF');
        $this->addSql('DROP INDEX IDX_5D4869B01137ABCF ON encaissement');
        $this->addSql('ALTER TABLE encaissement ADD rap INT DEFAULT NULL, ADD qte_restant INT DEFAULT NULL, CHANGE album_id vente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE encaissement ADD CONSTRAINT FK_5D4869B07DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('CREATE INDEX IDX_5D4869B07DC7170A ON encaissement (vente_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE vente ADD avance INT DEFAULT NULL, ADD reste INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE encaissement DROP FOREIGN KEY FK_5D4869B07DC7170A');
        $this->addSql('DROP INDEX IDX_5D4869B07DC7170A ON encaissement');
        $this->addSql('ALTER TABLE encaissement ADD album_id INT DEFAULT NULL, DROP vente_id, DROP rap, DROP qte_restant');
        $this->addSql('ALTER TABLE encaissement ADD CONSTRAINT FK_5D4869B01137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5D4869B01137ABCF ON encaissement (album_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE vente DROP avance, DROP reste');
    }
}
