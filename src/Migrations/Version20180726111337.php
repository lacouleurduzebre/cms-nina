<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180726111337 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_configuration ADD page_accueil_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nina_configuration ADD CONSTRAINT FK_B2A1BF5E777E0C5F FOREIGN KEY (page_accueil_id) REFERENCES nina_page (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B2A1BF5E777E0C5F ON nina_configuration (page_accueil_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_configuration DROP FOREIGN KEY FK_B2A1BF5E777E0C5F');
        $this->addSql('DROP INDEX UNIQ_B2A1BF5E777E0C5F ON nina_configuration');
        $this->addSql('ALTER TABLE nina_configuration DROP page_accueil_id');
    }
}
