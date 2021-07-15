<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210715205258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE section_templates (id INT UNSIGNED AUTO_INCREMENT NOT NULL, playthrough_template_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_D650D0BD6D5A38A (playthrough_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sections (id INT UNSIGNED AUTO_INCREMENT NOT NULL, playthrough_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_2B9643985F8BD68 (playthrough_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section_templates ADD CONSTRAINT FK_D650D0BD6D5A38A FOREIGN KEY (playthrough_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE sections ADD CONSTRAINT FK_2B9643985F8BD68 FOREIGN KEY (playthrough_id) REFERENCES playthroughs (id)');
        $this->addSql('ALTER TABLE playthrough_steps DROP FOREIGN KEY FK_89EE61535F8BD68');
        $this->addSql('ALTER TABLE playthrough_steps DROP FOREIGN KEY FK_89EE61537E3C61F9');
        $this->addSql('DROP INDEX IDX_89EE61537E3C61F9 ON playthrough_steps');
        $this->addSql('DROP INDEX IDX_89EE61535F8BD68 ON playthrough_steps');
        $this->addSql('ALTER TABLE playthrough_steps ADD section_id INT UNSIGNED NOT NULL, DROP owner_id, DROP playthrough_id');
        $this->addSql('ALTER TABLE playthrough_steps ADD CONSTRAINT FK_89EE6153D823E37A FOREIGN KEY (section_id) REFERENCES sections (id)');
        $this->addSql('CREATE INDEX IDX_89EE6153D823E37A ON playthrough_steps (section_id)');
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E45DA0FB8');
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E47E3C61F9');
        $this->addSql('DROP INDEX IDX_B21606E47E3C61F9 ON playthrough_template_steps');
        $this->addSql('DROP INDEX IDX_B21606E45DA0FB8 ON playthrough_template_steps');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD section_template_id INT UNSIGNED NOT NULL, DROP template_id, DROP owner_id');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E4132B70C7 FOREIGN KEY (section_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('CREATE INDEX IDX_B21606E4132B70C7 ON playthrough_template_steps (section_template_id)');
        $this->addSql('ALTER TABLE playthrough_templates ADD name VARCHAR(64) NOT NULL, ADD description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE playthroughs ADD name VARCHAR(64) NOT NULL, ADD description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_steps DROP FOREIGN KEY FK_89EE6153D823E37A');
        $this->addSql('DROP TABLE section_templates');
        $this->addSql('DROP TABLE sections');
        $this->addSql('DROP INDEX IDX_89EE6153D823E37A ON playthrough_steps');
        $this->addSql('ALTER TABLE playthrough_steps ADD playthrough_id INT UNSIGNED NOT NULL, CHANGE section_id owner_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE playthrough_steps ADD CONSTRAINT FK_89EE61535F8BD68 FOREIGN KEY (playthrough_id) REFERENCES playthroughs (id)');
        $this->addSql('ALTER TABLE playthrough_steps ADD CONSTRAINT FK_89EE61537E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_89EE61537E3C61F9 ON playthrough_steps (owner_id)');
        $this->addSql('CREATE INDEX IDX_89EE61535F8BD68 ON playthrough_steps (playthrough_id)');
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E4132B70C7');
        $this->addSql('DROP INDEX IDX_B21606E4132B70C7 ON playthrough_template_steps');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD owner_id INT UNSIGNED NOT NULL, CHANGE section_template_id template_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E45DA0FB8 FOREIGN KEY (template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_B21606E47E3C61F9 ON playthrough_template_steps (owner_id)');
        $this->addSql('CREATE INDEX IDX_B21606E45DA0FB8 ON playthrough_template_steps (template_id)');
        $this->addSql('ALTER TABLE playthrough_templates DROP name, DROP description');
        $this->addSql('ALTER TABLE playthroughs DROP name, DROP description');
    }
}
