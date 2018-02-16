<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180215120402 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, type_categorie_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, categorieParent VARCHAR(255) DEFAULT NULL, INDEX IDX_497DD6343BB65D28 (type_categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE champ (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, auteur VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, site VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, contenu LONGTEXT NOT NULL, corbeille TINYINT(1) NOT NULL, valide TINYINT(1) NOT NULL, idPage INT NOT NULL, INDEX IDX_67F068BC67F7E8BE (idPage), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, langue_defaut_id INT DEFAULT NULL, url LONGTEXT NOT NULL, logo LONGTEXT NOT NULL, nom VARCHAR(255) NOT NULL, emailContact VARCHAR(255) NOT NULL, emailMaintenance VARCHAR(255) NOT NULL, emailNewsletter VARCHAR(255) NOT NULL, analytics LONGTEXT NOT NULL, editeur VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A5E2A5D7968B03E4 (langue_defaut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langue (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, abreviation VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9357758E86B470F8 (abreviation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_page (id INT AUTO_INCREMENT NOT NULL, page_parent_id INT DEFAULT NULL, page_id INT NOT NULL, menu_id INT NOT NULL, position INT NOT NULL, INDEX IDX_DC45466E499475BF (page_parent_id), INDEX IDX_DC45466EC4663E4 (page_id), INDEX IDX_DC45466ECCD7E912 (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, contenu LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, auteur_derniere_modification_id INT DEFAULT NULL, page_parent_id INT DEFAULT NULL, page_originale_id INT DEFAULT NULL, langue_id INT DEFAULT NULL, seo_id INT DEFAULT NULL, titre LONGTEXT NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, date_publication DATETIME NOT NULL, date_depublication DATETIME DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, position INT NOT NULL, active TINYINT(1) NOT NULL, corbeille TINYINT(1) NOT NULL, INDEX IDX_140AB62060BB6FE6 (auteur_id), INDEX IDX_140AB620F6698E1C (auteur_derniere_modification_id), INDEX IDX_140AB620499475BF (page_parent_id), INDEX IDX_140AB620320E6035 (page_originale_id), INDEX IDX_140AB6202AADBACD (langue_id), UNIQUE INDEX UNIQ_140AB62097E3DD86 (seo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages_categories (page_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_533F7E1BC4663E4 (page_id), INDEX IDX_533F7E1BBCF5E72D (categorie_id), PRIMARY KEY(page_id, categorie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_module (page_id INT NOT NULL, module_id INT NOT NULL, INDEX IDX_63F2D036C4663E4 (page_id), INDEX IDX_63F2D036AFC2B591 (module_id), PRIMARY KEY(page_id, module_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seo (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, metaTitre LONGTEXT NOT NULL, metaDescription LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_module (id INT AUTO_INCREMENT NOT NULL, modules_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2FF34D106C6E55B5 (nom), INDEX IDX_2FF34D1060D6DC42 (modules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_module_champ (type_module_id INT NOT NULL, champ_id INT NOT NULL, INDEX IDX_A12E2AC61B04F481 (type_module_id), INDEX IDX_A12E2AC6D32AA90E (champ_id), PRIMARY KEY(type_module_id, champ_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_1D1C63B392FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1D1C63B3A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_1D1C63B3C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD6343BB65D28 FOREIGN KEY (type_categorie_id) REFERENCES categorie_type (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC67F7E8BE FOREIGN KEY (idPage) REFERENCES page (id)');
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D7968B03E4 FOREIGN KEY (langue_defaut_id) REFERENCES langue (id)');
        $this->addSql('ALTER TABLE menu_page ADD CONSTRAINT FK_DC45466E499475BF FOREIGN KEY (page_parent_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE menu_page ADD CONSTRAINT FK_DC45466EC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE menu_page ADD CONSTRAINT FK_DC45466ECCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB62060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620F6698E1C FOREIGN KEY (auteur_derniere_modification_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620499475BF FOREIGN KEY (page_parent_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620320E6035 FOREIGN KEY (page_originale_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6202AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB62097E3DD86 FOREIGN KEY (seo_id) REFERENCES seo (id)');
        $this->addSql('ALTER TABLE pages_categories ADD CONSTRAINT FK_533F7E1BC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pages_categories ADD CONSTRAINT FK_533F7E1BBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_module ADD CONSTRAINT FK_63F2D036C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_module ADD CONSTRAINT FK_63F2D036AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE type_module ADD CONSTRAINT FK_2FF34D1060D6DC42 FOREIGN KEY (modules_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE type_module_champ ADD CONSTRAINT FK_A12E2AC61B04F481 FOREIGN KEY (type_module_id) REFERENCES type_module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE type_module_champ ADD CONSTRAINT FK_A12E2AC6D32AA90E FOREIGN KEY (champ_id) REFERENCES champ (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages_categories DROP FOREIGN KEY FK_533F7E1BBCF5E72D');
        $this->addSql('ALTER TABLE type_module_champ DROP FOREIGN KEY FK_A12E2AC6D32AA90E');
        $this->addSql('ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7968B03E4');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6202AADBACD');
        $this->addSql('ALTER TABLE menu_page DROP FOREIGN KEY FK_DC45466ECCD7E912');
        $this->addSql('ALTER TABLE page_module DROP FOREIGN KEY FK_63F2D036AFC2B591');
        $this->addSql('ALTER TABLE type_module DROP FOREIGN KEY FK_2FF34D1060D6DC42');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC67F7E8BE');
        $this->addSql('ALTER TABLE menu_page DROP FOREIGN KEY FK_DC45466E499475BF');
        $this->addSql('ALTER TABLE menu_page DROP FOREIGN KEY FK_DC45466EC4663E4');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620499475BF');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620320E6035');
        $this->addSql('ALTER TABLE pages_categories DROP FOREIGN KEY FK_533F7E1BC4663E4');
        $this->addSql('ALTER TABLE page_module DROP FOREIGN KEY FK_63F2D036C4663E4');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB62097E3DD86');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD6343BB65D28');
        $this->addSql('ALTER TABLE type_module_champ DROP FOREIGN KEY FK_A12E2AC61B04F481');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB62060BB6FE6');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620F6698E1C');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE champ');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE langue');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_page');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE pages_categories');
        $this->addSql('DROP TABLE page_module');
        $this->addSql('DROP TABLE seo');
        $this->addSql('DROP TABLE categorie_type');
        $this->addSql('DROP TABLE type_module');
        $this->addSql('DROP TABLE type_module_champ');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE zone');
    }
}
