<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109225050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD famille_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6697A77B84 FOREIGN KEY (famille_id) REFERENCES famille (id)');
        $this->addSql('CREATE INDEX IDX_23A0E6697A77B84 ON article (famille_id)');
        $this->addSql('ALTER TABLE sous_famille DROP FOREIGN KEY FK_77A8A5E97A77B84');
        $this->addSql('DROP INDEX IDX_77A8A5E97A77B84 ON sous_famille');
        $this->addSql('ALTER TABLE sous_famille DROP famille_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6697A77B84');
        $this->addSql('DROP INDEX IDX_23A0E6697A77B84 ON article');
        $this->addSql('ALTER TABLE article DROP famille_id');
        $this->addSql('ALTER TABLE sous_famille ADD famille_id INT NOT NULL');
        $this->addSql('ALTER TABLE sous_famille ADD CONSTRAINT FK_77A8A5E97A77B84 FOREIGN KEY (famille_id) REFERENCES famille (id)');
        $this->addSql('CREATE INDEX IDX_77A8A5E97A77B84 ON sous_famille (famille_id)');
    }
}
