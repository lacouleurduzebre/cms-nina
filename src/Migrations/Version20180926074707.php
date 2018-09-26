<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180926074707 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_categorie ADD langue_id INT NOT NULL');
        $this->addSql('ALTER TABLE nina_categorie ADD CONSTRAINT FK_A8C99A692AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('CREATE INDEX IDX_A8C99A692AADBACD ON nina_categorie (langue_id)');
        $this->addSql('ALTER TABLE nina_type_categorie ADD langue_id INT NOT NULL');
        $this->addSql('ALTER TABLE nina_type_categorie ADD CONSTRAINT FK_69C8EFBA2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('CREATE INDEX IDX_69C8EFBA2AADBACD ON nina_type_categorie (langue_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_categorie DROP FOREIGN KEY FK_A8C99A692AADBACD');
        $this->addSql('DROP INDEX IDX_A8C99A692AADBACD ON nina_categorie');
        $this->addSql('ALTER TABLE nina_categorie DROP langue_id');
        $this->addSql('ALTER TABLE nina_type_categorie DROP FOREIGN KEY FK_69C8EFBA2AADBACD');
        $this->addSql('DROP INDEX IDX_69C8EFBA2AADBACD ON nina_type_categorie');
        $this->addSql('ALTER TABLE nina_type_categorie DROP langue_id');
    }
}
