<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180814125130 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_configuration DROP FOREIGN KEY FK_B2A1BF5E777E0C5F');
        $this->addSql('ALTER TABLE nina_configuration DROP FOREIGN KEY FK_B2A1BF5E968B03E4');
        $this->addSql('DROP INDEX UNIQ_B2A1BF5E968B03E4 ON nina_configuration');
        $this->addSql('DROP INDEX UNIQ_B2A1BF5E777E0C5F ON nina_configuration');
        $this->addSql('ALTER TABLE nina_configuration DROP langue_defaut_id, DROP page_accueil_id, DROP meta_description');
        $this->addSql('ALTER TABLE nina_langue ADD page_accueil_id INT DEFAULT NULL, ADD defaut TINYINT(1) NOT NULL, ADD meta_titre LONGTEXT DEFAULT NULL, ADD meta_description LONGTEXT DEFAULT NULL, ADD code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE nina_langue ADD CONSTRAINT FK_3B3F9408777E0C5F FOREIGN KEY (page_accueil_id) REFERENCES nina_page (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B3F9408777E0C5F ON nina_langue (page_accueil_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_configuration ADD langue_defaut_id INT DEFAULT NULL, ADD page_accueil_id INT DEFAULT NULL, ADD meta_description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE nina_configuration ADD CONSTRAINT FK_B2A1BF5E777E0C5F FOREIGN KEY (page_accueil_id) REFERENCES nina_page (id)');
        $this->addSql('ALTER TABLE nina_configuration ADD CONSTRAINT FK_B2A1BF5E968B03E4 FOREIGN KEY (langue_defaut_id) REFERENCES nina_langue (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B2A1BF5E968B03E4 ON nina_configuration (langue_defaut_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B2A1BF5E777E0C5F ON nina_configuration (page_accueil_id)');
        $this->addSql('ALTER TABLE nina_langue DROP FOREIGN KEY FK_3B3F9408777E0C5F');
        $this->addSql('DROP INDEX UNIQ_3B3F9408777E0C5F ON nina_langue');
        $this->addSql('ALTER TABLE nina_langue DROP page_accueil_id, DROP defaut, DROP meta_titre, DROP meta_description, DROP code');
    }
}
