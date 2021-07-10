<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210709011127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games ADD rating DOUBLE PRECISION DEFAULT NULL, ADD summary LONGTEXT DEFAULT NULL, ADD storyline LONGTEXT DEFAULT NULL, ADD screenshots LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD platforms LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD cover VARCHAR(64) DEFAULT NULL, ADD artworks LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', DROP developer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP rating, DROP summary, DROP storyline, DROP screenshots, DROP platforms, DROP cover, DROP artworks, CHANGE slug developer VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
