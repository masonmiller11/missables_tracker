<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210726221405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthroughs DROP FOREIGN KEY FK_DFEEC4385DA0FB8');
        $this->addSql('DROP INDEX IDX_DFEEC4385DA0FB8 ON playthroughs');
        $this->addSql('ALTER TABLE playthroughs CHANGE template_id template_id INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthroughs CHANGE template_id template_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC4385DA0FB8 FOREIGN KEY (template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('CREATE INDEX IDX_DFEEC4385DA0FB8 ON playthroughs (template_id)');
    }
}
