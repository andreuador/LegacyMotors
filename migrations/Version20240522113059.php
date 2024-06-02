<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522113059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD orders_id INT DEFAULT NULL, ADD invoices_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552454BA75 FOREIGN KEY (invoices_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_42C84955CFFE9AD6 ON reservation (orders_id)');
        $this->addSql('CREATE INDEX IDX_42C849552454BA75 ON reservation (invoices_id)');
        $this->addSql('ALTER TABLE review ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_794381C69395C3F3 ON review (customer_id)');
        $this->addSql('ALTER TABLE vehicle CHANGE description description VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vehicle CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955CFFE9AD6');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552454BA75');
        $this->addSql('DROP INDEX IDX_42C84955CFFE9AD6 ON reservation');
        $this->addSql('DROP INDEX IDX_42C849552454BA75 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP orders_id, DROP invoices_id');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C69395C3F3');
        $this->addSql('DROP INDEX IDX_794381C69395C3F3 ON review');
        $this->addSql('ALTER TABLE review DROP customer_id');
    }
}
