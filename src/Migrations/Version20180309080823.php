<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180309080823 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_type_module_champ DROP FOREIGN KEY FK_78496CD069FEEC45');
        $this->addSql('ALTER TABLE nina_type_module_champ DROP FOREIGN KEY FK_78496CD08F778C1F');
        $this->addSql('DROP TABLE nina_champ');
        $this->addSql('DROP TABLE nina_type_module');
        $this->addSql('DROP TABLE nina_type_module_champ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_champ (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_type_module (id INT AUTO_INCREMENT NOT NULL, modules_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_5F8C82066C6E55B5 (nom), INDEX IDX_5F8C820660D6DC42 (modules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_type_module_champ (nina_type_module_id INT NOT NULL, nina_champ_id INT NOT NULL, INDEX IDX_78496CD08F778C1F (nina_type_module_id), INDEX IDX_78496CD069FEEC45 (nina_champ_id), PRIMARY KEY(nina_type_module_id, nina_champ_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_type_module ADD CONSTRAINT FK_5F8C820660D6DC42 FOREIGN KEY (modules_id) REFERENCES nina_module (id)');
        $this->addSql('ALTER TABLE nina_type_module_champ ADD CONSTRAINT FK_78496CD069FEEC45 FOREIGN KEY (nina_champ_id) REFERENCES nina_champ (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nina_type_module_champ ADD CONSTRAINT FK_78496CD08F778C1F FOREIGN KEY (nina_type_module_id) REFERENCES nina_type_module (id) ON DELETE CASCADE');
    }
}
