<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180713064434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_module ADD page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nina_module ADD CONSTRAINT FK_A44CC7AEC4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('CREATE INDEX IDX_A44CC7AEC4663E4 ON nina_module (page_id)');
        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F6960D6DC42');
        $this->addSql('DROP INDEX IDX_2A202F6960D6DC42 ON nina_page');
        $this->addSql('ALTER TABLE nina_page DROP modules_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_module DROP FOREIGN KEY FK_A44CC7AEC4663E4');
        $this->addSql('DROP INDEX IDX_A44CC7AEC4663E4 ON nina_module');
        $this->addSql('ALTER TABLE nina_module DROP page_id');
        $this->addSql('ALTER TABLE nina_page ADD modules_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F6960D6DC42 FOREIGN KEY (modules_id) REFERENCES nina_module (id)');
        $this->addSql('CREATE INDEX IDX_2A202F6960D6DC42 ON nina_page (modules_id)');
    }
}
