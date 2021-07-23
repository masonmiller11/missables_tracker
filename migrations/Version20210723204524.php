<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210723204524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE templates_liked_by (user_id INT UNSIGNED NOT NULL, playthrough_template_id INT UNSIGNED NOT NULL, INDEX IDX_D0ED7DE3A76ED395 (user_id), INDEX IDX_D0ED7DE3D6D5A38A (playthrough_template_id), PRIMARY KEY(user_id, playthrough_template_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE templates_liked_by ADD CONSTRAINT FK_D0ED7DE3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE templates_liked_by ADD CONSTRAINT FK_D0ED7DE3D6D5A38A FOREIGN KEY (playthrough_template_id) REFERENCES playthrough_templates (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE templates_liked_by');
    }
}
