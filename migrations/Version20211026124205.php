<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211026124205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affiche (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, debut VARCHAR(255) DEFAULT NULL, fin VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, artiste_id INT DEFAULT NULL, genre_id INT DEFAULT NULL, titre VARCHAR(255) DEFAULT NULL, prix_vente INT DEFAULT NULL, non_sticke INT DEFAULT NULL, sticke INT DEFAULT NULL, distribue INT DEFAULT NULL, stock INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, pochette VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, ecommerce TINYINT(1) DEFAULT NULL, promotion TINYINT(1) DEFAULT NULL, frais_livraison TINYINT(1) DEFAULT NULL, INDEX IDX_39986E4321D25844 (artiste_id), INDEX IDX_39986E434296D31F (genre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artiste (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, nombre_album INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, matricule VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, localite_id INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, quantite INT DEFAULT NULL, montant INT DEFAULT NULL, id_transaction VARCHAR(255) DEFAULT NULL, status_transaction VARCHAR(255) DEFAULT NULL, tel_transaction VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, date_transaction VARCHAR(255) DEFAULT NULL, time_transaction VARCHAR(255) DEFAULT NULL, livraison TINYINT(1) DEFAULT NULL, INDEX IDX_6EEAA67D1137ABCF (album_id), INDEX IDX_6EEAA67D924DD2B5 (localite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE destockage (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, quantite INT DEFAULT NULL, motif LONGTEXT DEFAULT NULL, sticke_final INT DEFAULT NULL, INDEX IDX_82EC636C1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE encaissement (id INT AUTO_INCREMENT NOT NULL, vente_id INT DEFAULT NULL, recouvrement_id INT DEFAULT NULL, quantite INT DEFAULT NULL, montant INT DEFAULT NULL, rap INT DEFAULT NULL, qte_restant INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, flag TINYINT(1) DEFAULT NULL, INDEX IDX_5D4869B07DC7170A (vente_id), INDEX IDX_5D4869B043F3D39C (recouvrement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, vendeur_id INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, tva TINYINT(1) DEFAULT NULL, ttc INT DEFAULT NULL, flag TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, INDEX IDX_FE866410858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE localite (id INT AUTO_INCREMENT NOT NULL, lieu VARCHAR(255) DEFAULT NULL, prix INT DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, regroupement VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE precommande (id INT AUTO_INCREMENT NOT NULL, localite_id INT DEFAULT NULL, album_id INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, quantite INT DEFAULT NULL, montant INT DEFAULT NULL, flag TINYINT(1) DEFAULT NULL, id_transaction VARCHAR(255) DEFAULT NULL, status_transaction VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_7A250B45924DD2B5 (localite_id), INDEX IDX_7A250B451137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pressage (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, quantite INT DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, stock_final INT DEFAULT NULL, INDEX IDX_73354DDF1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recouvrement (id INT AUTO_INCREMENT NOT NULL, vendeur_id INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, numero_facture_distributeur VARCHAR(255) DEFAULT NULL, montant INT DEFAULT NULL, flag TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, restant INT DEFAULT NULL, INDEX IDX_7C5BE973858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slide (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, statut TINYINT(1) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stickage (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, quantite INT DEFAULT NULL, sticke_final INT DEFAULT NULL, non_sticke_final INT DEFAULT NULL, INDEX IDX_1CE5578F1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, connexion INT DEFAULT NULL, last_connected_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vendeur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, situation LONGTEXT DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, credit INT DEFAULT NULL, payer INT DEFAULT NULL, reste INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, album_id INT DEFAULT NULL, quantite INT DEFAULT NULL, pu INT DEFAULT NULL, montant INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, avance INT DEFAULT NULL, reste INT DEFAULT NULL, INDEX IDX_888A2A4C7F2DEE08 (facture_id), INDEX IDX_888A2A4C1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E4321D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E434296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D924DD2B5 FOREIGN KEY (localite_id) REFERENCES localite (id)');
        $this->addSql('ALTER TABLE destockage ADD CONSTRAINT FK_82EC636C1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE encaissement ADD CONSTRAINT FK_5D4869B07DC7170A FOREIGN KEY (vente_id) REFERENCES vente (id)');
        $this->addSql('ALTER TABLE encaissement ADD CONSTRAINT FK_5D4869B043F3D39C FOREIGN KEY (recouvrement_id) REFERENCES recouvrement (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410858C065E FOREIGN KEY (vendeur_id) REFERENCES vendeur (id)');
        $this->addSql('ALTER TABLE precommande ADD CONSTRAINT FK_7A250B45924DD2B5 FOREIGN KEY (localite_id) REFERENCES localite (id)');
        $this->addSql('ALTER TABLE precommande ADD CONSTRAINT FK_7A250B451137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE pressage ADD CONSTRAINT FK_73354DDF1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE recouvrement ADD CONSTRAINT FK_7C5BE973858C065E FOREIGN KEY (vendeur_id) REFERENCES vendeur (id)');
        $this->addSql('ALTER TABLE stickage ADD CONSTRAINT FK_1CE5578F1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE vente ADD CONSTRAINT FK_888A2A4C1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D1137ABCF');
        $this->addSql('ALTER TABLE destockage DROP FOREIGN KEY FK_82EC636C1137ABCF');
        $this->addSql('ALTER TABLE precommande DROP FOREIGN KEY FK_7A250B451137ABCF');
        $this->addSql('ALTER TABLE pressage DROP FOREIGN KEY FK_73354DDF1137ABCF');
        $this->addSql('ALTER TABLE stickage DROP FOREIGN KEY FK_1CE5578F1137ABCF');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C1137ABCF');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E4321D25844');
        $this->addSql('ALTER TABLE vente DROP FOREIGN KEY FK_888A2A4C7F2DEE08');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E434296D31F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D924DD2B5');
        $this->addSql('ALTER TABLE precommande DROP FOREIGN KEY FK_7A250B45924DD2B5');
        $this->addSql('ALTER TABLE encaissement DROP FOREIGN KEY FK_5D4869B043F3D39C');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410858C065E');
        $this->addSql('ALTER TABLE recouvrement DROP FOREIGN KEY FK_7C5BE973858C065E');
        $this->addSql('ALTER TABLE encaissement DROP FOREIGN KEY FK_5D4869B07DC7170A');
        $this->addSql('DROP TABLE affiche');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE artiste');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE destockage');
        $this->addSql('DROP TABLE encaissement');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE localite');
        $this->addSql('DROP TABLE precommande');
        $this->addSql('DROP TABLE pressage');
        $this->addSql('DROP TABLE recouvrement');
        $this->addSql('DROP TABLE slide');
        $this->addSql('DROP TABLE stickage');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vendeur');
        $this->addSql('DROP TABLE vente');
    }
}
