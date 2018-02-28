<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180228103105 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE nina_page_categorie (nina_page_id INT NOT NULL, nina_categorie_id INT NOT NULL, INDEX IDX_E6E011658352D10 (nina_page_id), INDEX IDX_E6E0116548512DB3 (nina_categorie_id), PRIMARY KEY(nina_page_id, nina_categorie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nina_page_categorie ADD CONSTRAINT FK_E6E011658352D10 FOREIGN KEY (nina_page_id) REFERENCES nina_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nina_page_categorie ADD CONSTRAINT FK_E6E0116548512DB3 FOREIGN KEY (nina_categorie_id) REFERENCES nina_categorie (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE pages_categories');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pages_categories (nina_page_id INT NOT NULL, nina_categorie_id INT NOT NULL, INDEX IDX_533F7E1B8352D10 (nina_page_id), INDEX IDX_533F7E1B48512DB3 (nina_categorie_id), PRIMARY KEY(nina_page_id, nina_categorie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pages_categories ADD CONSTRAINT FK_533F7E1B48512DB3 FOREIGN KEY (nina_categorie_id) REFERENCES nina_categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pages_categories ADD CONSTRAINT FK_533F7E1B8352D10 FOREIGN KEY (nina_page_id) REFERENCES nina_page (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE nina_page_categorie');
    }
}
