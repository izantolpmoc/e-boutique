<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230610140048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command_line (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, related_order_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_70BE1A7B4584665A (product_id), INDEX IDX_70BE1A7B2B1C2395 (related_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, valid TINYINT(1) NOT NULL, date DATETIME NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE command_line ADD CONSTRAINT FK_70BE1A7B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE command_line ADD CONSTRAINT FK_70BE1A7B2B1C2395 FOREIGN KEY (related_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command_line DROP FOREIGN KEY FK_70BE1A7B4584665A');
        $this->addSql('ALTER TABLE command_line DROP FOREIGN KEY FK_70BE1A7B2B1C2395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE command_line');
        $this->addSql('DROP TABLE `order`');
    }
}
