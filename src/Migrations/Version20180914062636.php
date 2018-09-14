<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180914062636 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_groupe_blocs (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, nom VARCHAR(255) NOT NULL, region VARCHAR(255) DEFAULT NULL, INDEX IDX_317E0BBA2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nina_groupe_blocs_bloc (nina_groupe_blocs_id INT NOT NULL, nina_bloc_id INT NOT NULL, INDEX IDX_8F19301DFAC13B37 (nina_groupe_blocs_id), INDEX IDX_8F19301D51F1A734 (nina_bloc_id), PRIMARY KEY(nina_groupe_blocs_id, nina_bloc_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_groupe_blocs ADD CONSTRAINT FK_317E0BBA2AADBACD FOREIGN KEY (langue_id) REFERENCES nina_langue (id)');
        $this->addSql('ALTER TABLE nina_groupe_blocs_bloc ADD CONSTRAINT FK_8F19301DFAC13B37 FOREIGN KEY (nina_groupe_blocs_id) REFERENCES nina_groupe_blocs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nina_groupe_blocs_bloc ADD CONSTRAINT FK_8F19301D51F1A734 FOREIGN KEY (nina_bloc_id) REFERENCES nina_bloc (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nina_groupe_blocs_bloc DROP FOREIGN KEY FK_8F19301DFAC13B37');
        $this->addSql('DROP TABLE nina_groupe_blocs');
        $this->addSql('DROP TABLE nina_groupe_blocs_bloc');
    }
}
