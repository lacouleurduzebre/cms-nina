<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180815094016 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F69320E6035');
        $this->addSql('DROP INDEX IDX_2A202F69320E6035 ON nina_page');
        $this->addSql('ALTER TABLE nina_page ADD traductions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', DROP page_originale_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_page ADD page_originale_id INT DEFAULT NULL, DROP traductions');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F69320E6035 FOREIGN KEY (page_originale_id) REFERENCES nina_page (id)');
        $this->addSql('CREATE INDEX IDX_2A202F69320E6035 ON nina_page (page_originale_id)');
    }
}
