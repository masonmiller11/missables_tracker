<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210716214216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E4132B70C7');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E4132B70C7 FOREIGN KEY (section_template_id) REFERENCES playthrough_template_sections (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E4132B70C7');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E4132B70C7 FOREIGN KEY (section_template_id) REFERENCES playthrough_templates (id)');
    }
}
