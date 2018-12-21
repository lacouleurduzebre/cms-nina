<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181221083427 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_bloc (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, groupe_blocs_id INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', position INT DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, html_avant VARCHAR(255) DEFAULT NULL, html_apres VARCHAR(255) DEFAULT NULL, INDEX IDX_F9520C13C4663E4 (page_id), INDEX IDX_F9520C13EDE2F60C (groupe_blocs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_bloc_annexe (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_7AC93F8FC4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_categorie (id INT AUTO_INCREMENT NOT NULL, type_categorie_id INT NOT NULL, langue_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, categorieParent VARCHAR(255) DEFAULT NULL, INDEX IDX_A8C99A693BB65D28 (type_categorie_id), INDEX IDX_A8C99A692AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_commentaire (id INT AUTO_INCREMENT NOT NULL, auteur VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, site VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, contenu LONGTEXT NOT NULL, corbeille TINYINT(1) NOT NULL, valide TINYINT(1) NOT NULL, idPage INT NOT NULL, INDEX IDX_178FA7AA67F7E8BE (idPage), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_configuration (id INT AUTO_INCREMENT NOT NULL, logo LONGTEXT NOT NULL, nom VARCHAR(255) NOT NULL, emailContact VARCHAR(255) NOT NULL, emailMaintenance VARCHAR(255) NOT NULL, analytics LONGTEXT DEFAULT NULL, editeur VARCHAR(255) NOT NULL, theme VARCHAR(255) DEFAULT NULL, maintenance TINYINT(1) NOT NULL, affichage_commentaires TINYINT(1) NOT NULL, affichage_date_publication TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_groupe_blocs (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, region_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, identifiant VARCHAR(255) NOT NULL, position SMALLINT DEFAULT NULL, INDEX IDX_317E0BBA2AADBACD (langue_id), INDEX IDX_317E0BBA98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_langue (id INT AUTO_INCREMENT NOT NULL, page_accueil_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, abreviation VARCHAR(191) NOT NULL, active TINYINT(1) NOT NULL, defaut TINYINT(1) NOT NULL, meta_titre VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3B3F940886B470F8 (abreviation), UNIQUE INDEX UNIQ_3B3F9408777E0C5F (page_accueil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_menu (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, nom VARCHAR(255) NOT NULL, defaut TINYINT(1) DEFAULT NULL, INDEX IDX_432FA3DA2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_menu_page (id INT AUTO_INCREMENT NOT NULL, page_parent_id INT DEFAULT NULL, page_id INT NOT NULL, menu_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_3DF10A33499475BF (page_parent_id), INDEX IDX_3DF10A33C4663E4 (page_id), INDEX IDX_3DF10A33CCD7E912 (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_page (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, auteur_derniere_modification_id INT DEFAULT NULL, langue_id INT DEFAULT NULL, seo_id INT DEFAULT NULL, titre LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, date_publication DATETIME NOT NULL, date_depublication DATETIME DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, corbeille TINYINT(1) NOT NULL, titre_menu VARCHAR(255) DEFAULT NULL, traductions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', affichage_sous_niveaux TINYINT(1) NOT NULL, affichage_commentaires TINYINT(1) NOT NULL, affichage_date_publication TINYINT(1) NOT NULL, INDEX IDX_2A202F6960BB6FE6 (auteur_id), INDEX IDX_2A202F69F6698E1C (auteur_derniere_modification_id), INDEX IDX_2A202F692AADBACD (langue_id), UNIQUE INDEX UNIQ_2A202F6997E3DD86 (seo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_page_categorie (nina_page_id INT NOT NULL, nina_categorie_id INT NOT NULL, INDEX IDX_E6E011658352D10 (nina_page_id), INDEX IDX_E6E0116548512DB3 (nina_categorie_id), PRIMARY KEY(nina_page_id, nina_categorie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, identifiant VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_seo (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, metaTitre LONGTEXT DEFAULT NULL, metaDescription LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_type_categorie (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_69C8EFBA2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_utilisateur (id INT AUTO_INCREMENT NOT NULL, langue_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', imageProfil LONGTEXT DEFAULT NULL, couleur_bo VARCHAR(255) DEFAULT NULL, blocs_tableau_de_bord LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_6D63ACA592FC23A8 (username_canonical), UNIQUE INDEX UNIQ_6D63ACA5A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_6D63ACA5C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_6D63ACA52AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_bloc ADD CONSTRAINT FK_F9520C13C4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_bloc ADD CONSTRAINT FK_F9520C13EDE2F60C FOREIGN KEY (groupe_blocs_id) REFERENCES nina_groupe_blocs (id)');
        $this->addSql('ALTER TABLE nina_bloc_annexe ADD CONSTRAINT FK_7AC93F8FC4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_categorie ADD CONSTRAINT FK_A8C99A693BB65D28 FOREIGN KEY (type_categorie_id) REFERENCES nina_type_categorie (id)');
        $this->addSql('ALTER TABLE nina_categorie ADD CONSTRAINT FK_A8C99A692AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_commentaire ADD CONSTRAINT FK_178FA7AA67F7E8BE FOREIGN KEY (idPage) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_groupe_blocs ADD CONSTRAINT FK_317E0BBA2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_groupe_blocs ADD CONSTRAINT FK_317E0BBA98260155 FOREIGN KEY (region_id) REFERENCES nina_region (id)');
        $this->addSql('ALTER TABLE nina_langue ADD CONSTRAINT FK_3B3F9408777E0C5F FOREIGN KEY (page_accueil_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_menu ADD CONSTRAINT FK_432FA3DA2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_menu_page ADD CONSTRAINT FK_3DF10A33499475BF FOREIGN KEY (page_parent_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_menu_page ADD CONSTRAINT FK_3DF10A33C4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_menu_page ADD CONSTRAINT FK_3DF10A33CCD7E912 FOREIGN KEY (menu_id) REFERENCES nina_menu (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F6960BB6FE6 FOREIGN KEY (auteur_id) REFERENCES nina_utilisateur (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F69F6698E1C FOREIGN KEY (auteur_derniere_modification_id) REFERENCES nina_utilisateur (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F692AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F6997E3DD86 FOREIGN KEY (seo_id) REFERENCES nina_seo (id)');
        $this->addSql('ALTER TABLE nina_page_categorie ADD CONSTRAINT FK_E6E011658352D10 FOREIGN KEY (nina_page_id) REFERENCES nina_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nina_page_categorie ADD CONSTRAINT FK_E6E0116548512DB3 FOREIGN KEY (nina_categorie_id) REFERENCES nina_categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nina_type_categorie ADD CONSTRAINT FK_69C8EFBA2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_utilisateur ADD CONSTRAINT FK_6D63ACA52AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_page_categorie DROP FOREIGN KEY FK_E6E0116548512DB3');
        $this->addSql('ALTER TABLE nina_bloc DROP FOREIGN KEY FK_F9520C13EDE2F60C');
        $this->addSql('ALTER TABLE nina_categorie DROP FOREIGN KEY FK_A8C99A692AADBACD');
        $this->addSql('ALTER TABLE nina_groupe_blocs DROP FOREIGN KEY FK_317E0BBA2AADBACD');
        $this->addSql('ALTER TABLE nina_menu DROP FOREIGN KEY FK_432FA3DA2AADBACD');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F692AADBACD');
        $this->addSql('ALTER TABLE nina_type_categorie DROP FOREIGN KEY FK_69C8EFBA2AADBACD');
        $this->addSql('ALTER TABLE nina_utilisateur DROP FOREIGN KEY FK_6D63ACA52AADBACD');
        $this->addSql('ALTER TABLE nina_menu_page DROP FOREIGN KEY FK_3DF10A33CCD7E912');
        $this->addSql('ALTER TABLE nina_bloc DROP FOREIGN KEY FK_F9520C13C4663E4');
        $this->addSql('ALTER TABLE nina_bloc_annexe DROP FOREIGN KEY FK_7AC93F8FC4663E4');
        $this->addSql('ALTER TABLE nina_commentaire DROP FOREIGN KEY FK_178FA7AA67F7E8BE');
        $this->addSql('ALTER TABLE nina_langue DROP FOREIGN KEY FK_3B3F9408777E0C5F');
        $this->addSql('ALTER TABLE nina_menu_page DROP FOREIGN KEY FK_3DF10A33499475BF');
        $this->addSql('ALTER TABLE nina_menu_page DROP FOREIGN KEY FK_3DF10A33C4663E4');
        $this->addSql('ALTER TABLE nina_page_categorie DROP FOREIGN KEY FK_E6E011658352D10');
        $this->addSql('ALTER TABLE nina_groupe_blocs DROP FOREIGN KEY FK_317E0BBA98260155');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F6997E3DD86');
        $this->addSql('ALTER TABLE nina_categorie DROP FOREIGN KEY FK_A8C99A693BB65D28');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F6960BB6FE6');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F69F6698E1C');
        $this->addSql('DROP TABLE nina_bloc');
        $this->addSql('DROP TABLE nina_bloc_annexe');
        $this->addSql('DROP TABLE nina_categorie');
        $this->addSql('DROP TABLE nina_commentaire');
        $this->addSql('DROP TABLE nina_configuration');
        $this->addSql('DROP TABLE nina_groupe_blocs');
        $this->addSql('DROP TABLE nina_langue');
        $this->addSql('DROP TABLE nina_menu');
        $this->addSql('DROP TABLE nina_menu_page');
        $this->addSql('DROP TABLE nina_page');
        $this->addSql('DROP TABLE nina_page_categorie');
        $this->addSql('DROP TABLE nina_region');
        $this->addSql('DROP TABLE nina_seo');
        $this->addSql('DROP TABLE nina_type_categorie');
        $this->addSql('DROP TABLE nina_utilisateur');
    }
}
