<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180820091424 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_bloc (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', position INT DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, INDEX IDX_F9520C13C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_bloc ADD CONSTRAINT FK_F9520C13C4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('DROP TABLE nina_module');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_module (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, contenu LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', position INT DEFAULT NULL, class VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_A44CC7AEC4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_module ADD CONSTRAINT FK_A44CC7AEC4663E4 FOREIGN KEY (page_id) REFERENCES nina_page (id)');
        $this->addSql('DROP TABLE nina_bloc');
    }
}
