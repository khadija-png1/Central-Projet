<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250604124847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE developpeur CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hebergement CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE technologie CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE developpeur CHANGE created created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated updated DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hebergement CHANGE created created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated updated DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet CHANGE created created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated updated DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE technologie CHANGE created created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated updated DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE created created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE updated updated DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
    }
}
