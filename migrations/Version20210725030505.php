<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210725030505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, release_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', genre VARCHAR(64) NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, summary LONGTEXT DEFAULT NULL, storyline LONGTEXT DEFAULT NULL, slug VARCHAR(64) NOT NULL, screenshots LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', platforms LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', cover VARCHAR(64) DEFAULT NULL, artworks LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', internet_game_database_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_FF232B31FDCD9D5B (internet_game_database_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE igdb_config (id INT UNSIGNED AUTO_INCREMENT NOT NULL, token VARCHAR(128) NOT NULL, expiration DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', generated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_sections (id INT UNSIGNED AUTO_INCREMENT NOT NULL, playthrough_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, position INT UNSIGNED NOT NULL, INDEX IDX_6C253055F8BD68 (playthrough_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_steps (id INT UNSIGNED AUTO_INCREMENT NOT NULL, section_id INT UNSIGNED NOT NULL, completed TINYINT(1) NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, position INT UNSIGNED NOT NULL, INDEX IDX_89EE6153D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_template_sections (id INT UNSIGNED AUTO_INCREMENT NOT NULL, playthrough_template_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, position INT UNSIGNED NOT NULL, INDEX IDX_5F093CE5D6D5A38A (playthrough_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_template_steps (id INT UNSIGNED AUTO_INCREMENT NOT NULL, section_template_id INT UNSIGNED NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, position INT UNSIGNED NOT NULL, INDEX IDX_B21606E4132B70C7 (section_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_templates (id INT UNSIGNED AUTO_INCREMENT NOT NULL, game_id INT UNSIGNED NOT NULL, owner_id INT UNSIGNED NOT NULL, visibility TINYINT(1) NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_E1BBC667E48FD905 (game_id), INDEX IDX_E1BBC6677E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthrough_templates_likes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, liked_by_id INT UNSIGNED DEFAULT NULL, liked_template_id INT UNSIGNED DEFAULT NULL, INDEX IDX_D205F374B4622EC2 (liked_by_id), INDEX IDX_D205F374B1648403 (liked_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playthroughs (id INT UNSIGNED AUTO_INCREMENT NOT NULL, game_id INT UNSIGNED NOT NULL, template_id INT UNSIGNED NOT NULL, owner_id INT UNSIGNED NOT NULL, visibility TINYINT(1) NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_DFEEC438E48FD905 (game_id), INDEX IDX_DFEEC4385DA0FB8 (template_id), INDEX IDX_DFEEC4387E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(254) NOT NULL, username VARCHAR(254) NOT NULL, password LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playthrough_sections ADD CONSTRAINT FK_6C253055F8BD68 FOREIGN KEY (playthrough_id) REFERENCES playthroughs (id)');
        $this->addSql('ALTER TABLE playthrough_steps ADD CONSTRAINT FK_89EE6153D823E37A FOREIGN KEY (section_id) REFERENCES playthrough_sections (id)');
        $this->addSql('ALTER TABLE playthrough_template_sections ADD CONSTRAINT FK_5F093CE5D6D5A38A FOREIGN KEY (playthrough_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthrough_template_steps ADD CONSTRAINT FK_B21606E4132B70C7 FOREIGN KEY (section_template_id) REFERENCES playthrough_template_sections (id)');
        $this->addSql('ALTER TABLE playthrough_templates ADD CONSTRAINT FK_E1BBC667E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE playthrough_templates ADD CONSTRAINT FK_E1BBC6677E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE playthrough_templates_likes ADD CONSTRAINT FK_D205F374B4622EC2 FOREIGN KEY (liked_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE playthrough_templates_likes ADD CONSTRAINT FK_D205F374B1648403 FOREIGN KEY (liked_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC438E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC4385DA0FB8 FOREIGN KEY (template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthroughs ADD CONSTRAINT FK_DFEEC4387E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playthrough_templates DROP FOREIGN KEY FK_E1BBC667E48FD905');
        $this->addSql('ALTER TABLE playthroughs DROP FOREIGN KEY FK_DFEEC438E48FD905');
        $this->addSql('ALTER TABLE playthrough_steps DROP FOREIGN KEY FK_89EE6153D823E37A');
        $this->addSql('ALTER TABLE playthrough_template_steps DROP FOREIGN KEY FK_B21606E4132B70C7');
        $this->addSql('ALTER TABLE playthrough_template_sections DROP FOREIGN KEY FK_5F093CE5D6D5A38A');
        $this->addSql('ALTER TABLE playthrough_templates_likes DROP FOREIGN KEY FK_D205F374B1648403');
        $this->addSql('ALTER TABLE playthroughs DROP FOREIGN KEY FK_DFEEC4385DA0FB8');
        $this->addSql('ALTER TABLE playthrough_sections DROP FOREIGN KEY FK_6C253055F8BD68');
        $this->addSql('ALTER TABLE playthrough_templates DROP FOREIGN KEY FK_E1BBC6677E3C61F9');
        $this->addSql('ALTER TABLE playthrough_templates_likes DROP FOREIGN KEY FK_D205F374B4622EC2');
        $this->addSql('ALTER TABLE playthroughs DROP FOREIGN KEY FK_DFEEC4387E3C61F9');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE igdb_config');
        $this->addSql('DROP TABLE playthrough_sections');
        $this->addSql('DROP TABLE playthrough_steps');
        $this->addSql('DROP TABLE playthrough_template_sections');
        $this->addSql('DROP TABLE playthrough_template_steps');
        $this->addSql('DROP TABLE playthrough_templates');
        $this->addSql('DROP TABLE playthrough_templates_likes');
        $this->addSql('DROP TABLE playthroughs');
        $this->addSql('DROP TABLE users');
    }
}
