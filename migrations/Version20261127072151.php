<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261127072151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, category VARCHAR(20) NOT NULL, location VARCHAR(255) DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, price_per_person NUMERIC(10, 2) NOT NULL, max_participants INT DEFAULT NULL, booking_url VARCHAR(255) DEFAULT NULL, trip_id INT NOT NULL, created_by_id INT DEFAULT NULL, INDEX IDX_AC74095AA5BC2E0E (trip_id), INDEX IDX_AC74095AB03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE activity_participant (activity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D911011D81C06096 (activity_id), INDEX IDX_D911011DA76ED395 (user_id), PRIMARY KEY (activity_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(150) NOT NULL, amount NUMERIC(10, 2) NOT NULL, paid_at DATE NOT NULL, trip_id INT NOT NULL, paid_by_id INT NOT NULL, activity_id INT DEFAULT NULL, INDEX IDX_2D3A8DA6A5BC2E0E (trip_id), INDEX IDX_2D3A8DA67F9BC654 (paid_by_id), INDEX IDX_2D3A8DA681C06096 (activity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE expense_shared_with (expense_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5B0A250DF395DB7B (expense_id), INDEX IDX_5B0A250DA76ED395 (user_id), PRIMARY KEY (expense_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, destination VARCHAR(150) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, budget_per_person NUMERIC(10, 2) DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_7656F53B7E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE trip_member (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, joined_at DATETIME NOT NULL, trip_id INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_TRIP_USER (trip_id, user_id), INDEX IDX_9FEBA1D2A5BC2E0E (trip_id), INDEX IDX_9FEBA1D2A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE activity_participant ADD CONSTRAINT FK_D911011D81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_participant ADD CONSTRAINT FK_D911011DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA67F9BC654 FOREIGN KEY (paid_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE expense_shared_with ADD CONSTRAINT FK_5B0A250DF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_shared_with ADD CONSTRAINT FK_5B0A250DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE trip_member ADD CONSTRAINT FK_9FEBA1D2A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_member ADD CONSTRAINT FK_9FEBA1D2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AA5BC2E0E');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AB03A8386');
        $this->addSql('ALTER TABLE activity_participant DROP FOREIGN KEY FK_D911011D81C06096');
        $this->addSql('ALTER TABLE activity_participant DROP FOREIGN KEY FK_D911011DA76ED395');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6A5BC2E0E');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA67F9BC654');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA681C06096');
        $this->addSql('ALTER TABLE expense_shared_with DROP FOREIGN KEY FK_5B0A250DF395DB7B');
        $this->addSql('ALTER TABLE expense_shared_with DROP FOREIGN KEY FK_5B0A250DA76ED395');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B7E3C61F9');
        $this->addSql('ALTER TABLE trip_member DROP FOREIGN KEY FK_9FEBA1D2A5BC2E0E');
        $this->addSql('ALTER TABLE trip_member DROP FOREIGN KEY FK_9FEBA1D2A76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_participant');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_shared_with');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE trip_member');
        $this->addSql('DROP TABLE `user`');
    }
}
