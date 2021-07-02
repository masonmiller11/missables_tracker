<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210701221406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E4D6D5A38A');
        $this->addSql('DROP INDEX IDX_B21606E4D6D5A38A ON playthrough_template_steps');
        $this->addSql('ALTER TABLE playthrough_template_steps CHANGE playthrough_template_id template_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E45DA0FB8 FOREIGN KEY (template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('CREATE INDEX IDX_B21606E45DA0FB8 ON playthrough_template_steps (template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E45DA0FB8');
        $this->addSql('DROP INDEX IDX_B21606E45DA0FB8 ON playthrough_template_steps');
        $this->addSql('ALTER TABLE playthrough_template_steps CHANGE template_id playthrough_template_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E4D6D5A38A FOREIGN KEY (playthrough_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('CREATE INDEX IDX_B21606E4D6D5A38A ON playthrough_template_steps (playthrough_template_id)');
    }
}
