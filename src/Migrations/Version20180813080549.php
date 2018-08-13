<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180813080549 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_menu ADD langue_id INT NOT NULL');
        $this->addSql('ALTER TABLE nina_menu ADD CONSTRAINT FK_432FA3DA2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('CREATE INDEX IDX_432FA3DA2AADBACD ON nina_menu (langue_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_menu DROP FOREIGN KEY FK_432FA3DA2AADBACD');
        $this->addSql('DROP INDEX IDX_432FA3DA2AADBACD ON nina_menu');
        $this->addSql('ALTER TABLE nina_menu DROP langue_id');
    }
}
