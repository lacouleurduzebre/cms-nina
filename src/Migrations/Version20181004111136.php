<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181004111136 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_groupe_blocs ADD region_id INT DEFAULT NULL, DROP region');
        $this->addSql('ALTER TABLE nina_groupe_blocs ADD CONSTRAINT FK_317E0BBA98260155 FOREIGN KEY (region_id) REFERENCES nina_region (id)');
        $this->addSql('CREATE INDEX IDX_317E0BBA98260155 ON nina_groupe_blocs (region_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_groupe_blocs DROP FOREIGN KEY FK_317E0BBA98260155');
        $this->addSql('DROP TABLE nina_region');
        $this->addSql('DROP INDEX IDX_317E0BBA98260155 ON nina_groupe_blocs');
        $this->addSql('ALTER TABLE nina_groupe_blocs ADD region VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP region_id');
    }
}
