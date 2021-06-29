<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629224841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, developer VARCHAR(64) NOT NULL, release_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', genre VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_steps (id INT UNSIGNED AUTO_INCREMENT NOT NULL, owner_id INT UNSIGNED NOT NULL, playthrough_id INT UNSIGNED NOT NULL, completed TINYINT(1) NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_89EE61537E3C61F9 (owner_id), INDEX IDX_89EE61535F8BD68 (playthrough_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_template_steps (id INT UNSIGNED AUTO_INCREMENT NOT NULL, playthrough_template_id INT UNSIGNED NOT NULL, owner_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_B21606E4D6D5A38A (playthrough_template_id), INDEX IDX_B21606E47E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_templates (id INT UNSIGNED AUTO_INCREMENT NOT NULL, game_id INT UNSIGNED NOT NULL, owner_id INT UNSIGNED NOT NULL, visibility TINYINT(1) NOT NULL, votes INT NOT NULL, INDEX IDX_E1BBC667E48FD905 (game_id), INDEX IDX_E1BBC6677E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthroughs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, game_id INT UNSIGNED NOT NULL, template_id INT UNSIGNED NOT NULL, owner_id INT UNSIGNED NOT NULL, visibility TINYINT(1) NOT NULL, INDEX IDX_DFEEC438E48FD905 (game_id), INDEX IDX_DFEEC4385DA0FB8 (template_id), INDEX IDX_DFEEC4387E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playthrough_steps ADD CONSTRAINT FK_89EE61537E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE playthrough_steps ADD CONSTRAINT FK_89EE61535F8BD68 FOREIGN KEY (playthrough_id) REFERENCES playthroughs (id)');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E4D6D5A38A FOREIGN KEY (playthrough_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE playthrough_templates ADD CONSTRAINT FK_E1BBC667E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE playthrough_templates ADD CONSTRAINT FK_E1BBC6677E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC438E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC4385DA0FB8 FOREIGN KEY (template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC4387E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_templates DROP FOREIGN KEY FK_E1BBC667E48FD905');
        $this->addSql('ALTER TABLE playthroughs DROP FOREIGN KEY FK_DFEEC438E48FD905');
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E4D6D5A38A');
        $this->addSql('ALTER TABLE playthroughs DROP FOREIGN KEY FK_DFEEC4385DA0FB8');
        $this->addSql('ALTER TABLE playthrough_steps DROP FOREIGN KEY FK_89EE61535F8BD68');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE playthrough_steps');
        $this->addSql('DROP TABLE playthrough_template_steps');
        $this->addSql('DROP TABLE playthrough_templates');
        $this->addSql('DROP TABLE playthroughs');
    }
}
