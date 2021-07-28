<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210728013005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playthrough_template_likes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, liked_by_id INT UNSIGNED DEFAULT NULL, liked_template_id INT UNSIGNED DEFAULT NULL, INDEX IDX_CFFE42EBB4622EC2 (liked_by_id), INDEX IDX_CFFE42EBB1648403 (liked_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playthrough_template_likes ADD CONSTRAINT FK_CFFE42EBB4622EC2 FOREIGN KEY (liked_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE playthrough_template_likes ADD CONSTRAINT FK_CFFE42EBB1648403 FOREIGN KEY (liked_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('DROP TABLE playthrough_templates_likes');
        $this->addSql('ALTER TABLE playthrough_templates ADD number_of_likes INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playthrough_templates_likes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, liked_by_id INT UNSIGNED DEFAULT NULL, liked_template_id INT UNSIGNED DEFAULT NULL, INDEX IDX_D205F374B1648403 (liked_template_id), INDEX IDX_D205F374B4622EC2 (liked_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE playthrough_templates_likes ADD CONSTRAINT FK_D205F374B1648403 FOREIGN KEY (liked_template_id) REFERENCES playthrough_templates (id)');
        $this->addSql('ALTER TABLE playthrough_templates_likes ADD CONSTRAINT FK_D205F374B4622EC2 FOREIGN KEY (liked_by_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE playthrough_template_likes');
        $this->addSql('ALTER TABLE playthrough_templates DROP number_of_likes');
    }
}
