<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180727062612 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_utilisateur ADD langue_id INT DEFAULT NULL, ADD couleur_bo VARCHAR(255) DEFAULT NULL, ADD blocs_tableau_de_bord LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE nina_utilisateur ADD CONSTRAINT FK_6D63ACA52AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D63ACA52AADBACD ON nina_utilisateur (langue_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_utilisateur DROP FOREIGN KEY FK_6D63ACA52AADBACD');
        $this->addSql('DROP INDEX UNIQ_6D63ACA52AADBACD ON nina_utilisateur');
        $this->addSql('ALTER TABLE nina_utilisateur DROP langue_id, DROP couleur_bo, DROP blocs_tableau_de_bord');
    }
}
