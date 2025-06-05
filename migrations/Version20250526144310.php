<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250526144310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE projet_technologie (projet_id INT NOT NULL, technologie_id INT NOT NULL, INDEX IDX_76BB624AC18272 (projet_id), INDEX IDX_76BB624A261A27D2 (technologie_id), PRIMARY KEY(projet_id, technologie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projet_developpeur (projet_id INT NOT NULL, developpeur_id INT NOT NULL, INDEX IDX_F9CA94F9C18272 (projet_id), INDEX IDX_F9CA94F984E66085 (developpeur_id), PRIMARY KEY(projet_id, developpeur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projet_hebergement (projet_id INT NOT NULL, hebergement_id INT NOT NULL, INDEX IDX_936889A2C18272 (projet_id), INDEX IDX_936889A223BB0F66 (hebergement_id), PRIMARY KEY(projet_id, hebergement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_technologie ADD CONSTRAINT FK_76BB624AC18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_technologie ADD CONSTRAINT FK_76BB624A261A27D2 FOREIGN KEY (technologie_id) REFERENCES technologie (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_developpeur ADD CONSTRAINT FK_F9CA94F9C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_developpeur ADD CONSTRAINT FK_F9CA94F984E66085 FOREIGN KEY (developpeur_id) REFERENCES developpeur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_hebergement ADD CONSTRAINT FK_936889A2C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_hebergement ADD CONSTRAINT FK_936889A223BB0F66 FOREIGN KEY (hebergement_id) REFERENCES hebergement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD type VARCHAR(255) DEFAULT NULL, ADD acces_code_source LONGTEXT DEFAULT NULL, ADD acces_environnement LONGTEXT DEFAULT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE created created DATETIME NOT NULL, CHANGE updated updated DATETIME NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_technologie DROP FOREIGN KEY FK_76BB624AC18272
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_technologie DROP FOREIGN KEY FK_76BB624A261A27D2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_developpeur DROP FOREIGN KEY FK_F9CA94F9C18272
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_developpeur DROP FOREIGN KEY FK_F9CA94F984E66085
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_hebergement DROP FOREIGN KEY FK_936889A2C18272
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet_hebergement DROP FOREIGN KEY FK_936889A223BB0F66
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projet_technologie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projet_developpeur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projet_hebergement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP type, DROP acces_code_source, DROP acces_environnement, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE created created DATETIME DEFAULT NULL, CHANGE updated updated DATETIME DEFAULT NULL
        SQL);
    }
}
