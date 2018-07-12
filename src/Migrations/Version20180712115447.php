<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180712115447 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_module (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, contenu LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_page ADD modules_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nina_page ADD CONSTRAINT FK_2A202F6960D6DC42 FOREIGN KEY (modules_id) REFERENCES nina_module (id)');
        $this->addSql('CREATE INDEX IDX_2A202F6960D6DC42 ON nina_page (modules_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_page DROP FOREIGN KEY FK_2A202F6960D6DC42');
        $this->addSql('DROP TABLE nina_module');
        $this->addSql('DROP INDEX IDX_2A202F6960D6DC42 ON nina_page');
        $this->addSql('ALTER TABLE nina_page DROP modules_id');
    }
}
