<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180813084442 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_zone ADD langue_id INT NOT NULL');
        $this->addSql('ALTER TABLE nina_zone ADD CONSTRAINT FK_9EC1594E2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('CREATE INDEX IDX_9EC1594E2AADBACD ON nina_zone (langue_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_zone DROP FOREIGN KEY FK_9EC1594E2AADBACD');
        $this->addSql('DROP INDEX IDX_9EC1594E2AADBACD ON nina_zone');
        $this->addSql('ALTER TABLE nina_zone DROP langue_id');
    }
}
