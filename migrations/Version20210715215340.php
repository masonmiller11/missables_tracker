<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210715215340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_steps ADD position INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD position INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE section_templates ADD position INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE sections ADD position INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_steps DROP position');
        $this->addSql('ALTER TABLE playthrough_template_steps DROP position');
        $this->addSql('ALTER TABLE section_templates DROP position');
        $this->addSql('ALTER TABLE sections DROP position');
    }
}
