<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180309075052 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_module_image (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_module_texte (id INT AUTO_INCREMENT NOT NULL, texte LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_module DROP FOREIGN KEY FK_A44CC7AEC54C8C93');
        $this->addSql('DROP INDEX IDX_A44CC7AEC54C8C93 ON nina_module');
        $this->addSql('ALTER TABLE nina_module ADD type VARCHAR(255) NOT NULL, DROP type_id, DROP contenu');
        $this->addSql('ALTER TABLE nina_type_module ADD modules_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nina_type_module ADD CONSTRAINT FK_5F8C820660D6DC42 FOREIGN KEY (modules_id) REFERENCES nina_module (id)');
        $this->addSql('CREATE INDEX IDX_5F8C820660D6DC42 ON nina_type_module (modules_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE nina_module_image');
        $this->addSql('DROP TABLE nina_module_texte');
        $this->addSql('ALTER TABLE nina_module ADD type_id INT DEFAULT NULL, ADD contenu LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', DROP type');
        $this->addSql('ALTER TABLE nina_module ADD CONSTRAINT FK_A44CC7AEC54C8C93 FOREIGN KEY (type_id) REFERENCES nina_type_module (id)');
        $this->addSql('CREATE INDEX IDX_A44CC7AEC54C8C93 ON nina_module (type_id)');
        $this->addSql('ALTER TABLE nina_type_module DROP FOREIGN KEY FK_5F8C820660D6DC42');
        $this->addSql('DROP INDEX IDX_5F8C820660D6DC42 ON nina_type_module');
        $this->addSql('ALTER TABLE nina_type_module DROP modules_id');
    }
}
