<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180704065221 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_categorie (id INT AUTO_INCREMENT NOT NULL, type_categorie_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, categorieParent VARCHAR(255) DEFAULT NULL, INDEX IDX_A8C99A693BB65D28 (type_categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_commentaire (id INT AUTO_INCREMENT NOT NULL, auteur VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, site VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, contenu LONGTEXT NOT NULL, corbeille TINYINT(1) NOT NULL, valide TINYINT(1) NOT NULL, idPage INT NOT NULL, INDEX IDX_178FA7AA67F7E8BE (idPage), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_configuration (id INT AUTO_INCREMENT NOT NULL, langue_defaut_id INT DEFAULT NULL, url LONGTEXT NOT NULL, logo LONGTEXT NOT NULL, nom VARCHAR(255) NOT NULL, emailContact VARCHAR(255) NOT NULL, emailMaintenance VARCHAR(255) NOT NULL, emailNewsletter VARCHAR(255) NOT NULL, analytics LONGTEXT NOT NULL, editeur VARCHAR(255) NOT NULL, theme VARCHAR(255) DEFAULT NULL, maj DATETIME NOT NULL, UNIQUE INDEX UNIQ_B2A1BF5E968B03E4 (langue_defaut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_langue (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, abreviation VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_3B3F940886B470F8 (abreviation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_menu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_menu_page (id INT AUTO_INCREMENT NOT NULL, page_parent_id INT DEFAULT NULL, page_id INT NOT NULL, menu_id INT NOT NULL, position INT NOT NULL, INDEX IDX_3DF10A33499475BF (page_parent_id), INDEX IDX_3DF10A33C4663E4 (page_id), INDEX IDX_3DF10A33CCD7E912 (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_page (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, auteur_derniere_modification_id INT DEFAULT NULL, page_originale_id INT DEFAULT NULL, langue_id INT DEFAULT NULL, seo_id INT DEFAULT NULL, titre LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, date_publication DATETIME NOT NULL, date_depublication DATETIME DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, corbeille TINYINT(1) NOT NULL, contenu LONGTEXT DEFAULT NULL, INDEX IDX_2A202F6960BB6FE6 (auteur_id), INDEX IDX_2A202F69F6698E1C (auteur_derniere_modification_id), INDEX IDX_2A202F69320E6035 (page_originale_id), INDEX IDX_2A202F692AADBACD (langue_id), UNIQUE INDEX UNIQ_2A202F6997E3DD86 (seo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_page_categorie (nina_page_id INT NOT NULL, nina_categorie_id INT NOT NULL, INDEX IDX_E6E011658352D10 (nina_page_id), INDEX IDX_E6E0116548512DB3 (nina_categorie_id), PRIMARY KEY(nina_page_id, nina_categorie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_seo (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, metaTitre LONGTEXT NOT NULL, metaDescription LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_type_categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_utilisateur (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_6D63ACA592FC23A8 (username_canonical), UNIQUE INDEX UNIQ_6D63ACA5A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_6D63ACA5C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_zone (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_categorie ADD CONSTRAINT FK_A8C99A693BB65D28 FOREIGN KEY (type_categorie_id) REFERENCES nina_type_categorie (id)');
        $this->addSql('ALTER TABLE nina_commentaire ADD CONSTRAINT FK_178FA7AA67F7E8BE FOREIGN KEY (idPage) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_configuration ADD CONSTRAINT FK_B2A1BF5E968B03E4 FOREIGN KEY (langue_defaut_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_menu_page ADD CONSTRAINT FK_3DF10A33499475BF FOREIGN KEY (page_parent_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_menu_page ADD CONSTRAINT FK_3DF10A33C4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_menu_page ADD CONSTRAINT FK_3DF10A33CCD7E912 FOREIGN KEY (menu_id) REFERENCES nina_menu (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F6960BB6FE6 FOREIGN KEY (auteur_id) REFERENCES nina_utilisateur (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F69F6698E1C FOREIGN KEY (auteur_derniere_modification_id) REFERENCES nina_utilisateur (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F69320E6035 FOREIGN KEY (page_originale_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F692AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F6997E3DD86 FOREIGN KEY (seo_id) REFERENCES nina_seo (id)');
        $this->addSql('ALTER TABLE nina_page_categorie ADD CONSTRAINT FK_E6E011658352D10 FOREIGN KEY (nina_page_id) REFERENCES nina_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nina_page_categorie ADD CONSTRAINT FK_E6E0116548512DB3 FOREIGN KEY (nina_categorie_id) REFERENCES nina_categorie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_page_categorie DROP FOREIGN KEY FK_E6E0116548512DB3');
        $this->addSql('ALTER TABLE nina_configuration DROP FOREIGN KEY FK_B2A1BF5E968B03E4');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F692AADBACD');
        $this->addSql('ALTER TABLE nina_menu_page DROP FOREIGN KEY FK_3DF10A33CCD7E912');
        $this->addSql('ALTER TABLE nina_commentaire DROP FOREIGN KEY FK_178FA7AA67F7E8BE');
        $this->addSql('ALTER TABLE nina_menu_page DROP FOREIGN KEY FK_3DF10A33499475BF');
        $this->addSql('ALTER TABLE nina_menu_page DROP FOREIGN KEY FK_3DF10A33C4663E4');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F69320E6035');
        $this->addSql('ALTER TABLE nina_page_categorie DROP FOREIGN KEY FK_E6E011658352D10');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F6997E3DD86');
        $this->addSql('ALTER TABLE nina_categorie DROP FOREIGN KEY FK_A8C99A693BB65D28');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F6960BB6FE6');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F69F6698E1C');
        $this->addSql('DROP TABLE nina_categorie');
        $this->addSql('DROP TABLE nina_commentaire');
        $this->addSql('DROP TABLE nina_configuration');
        $this->addSql('DROP TABLE nina_langue');
        $this->addSql('DROP TABLE nina_menu');
        $this->addSql('DROP TABLE nina_menu_page');
        $this->addSql('DROP TABLE nina_page');
        $this->addSql('DROP TABLE nina_page_categorie');
        $this->addSql('DROP TABLE nina_seo');
        $this->addSql('DROP TABLE nina_type_categorie');
        $this->addSql('DROP TABLE nina_utilisateur');
        $this->addSql('DROP TABLE nina_zone');
    }
}
