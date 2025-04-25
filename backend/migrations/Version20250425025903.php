<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425025903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE badge (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN badge.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE badge_user (badge_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(badge_id, user_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_299D3A50F7A2C2FC ON badge_user (badge_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_299D3A50A76ED395 ON badge_user (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id SERIAL NOT NULL, user_id_id INT NOT NULL, room_id_id INT NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, final_price INT NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE9D86650F ON booking (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE35F83FFC ON booking (room_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN booking.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN booking.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hotel (id SERIAL NOT NULL, owner_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code INT NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3535ED98FDDAB70 ON hotel (owner_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE image (id SERIAL NOT NULL, hotel_id INT DEFAULT NULL, room_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C53D045F3243BB18 ON image (hotel_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C53D045F54177093 ON image (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id SERIAL NOT NULL, negotiation_id_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F7C32323B ON message (negotiation_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE negotiation (id SERIAL NOT NULL, proposed_price INT NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN negotiation.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN negotiation.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE refresh_token (id VARCHAR(36) NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C74F21955F37A13B ON refresh_token (token)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C74F2195A76ED395 ON refresh_token (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room (id SERIAL NOT NULL, hotel_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, capacity INT NOT NULL, price INT NOT NULL, available BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_729F519B9C905093 ON room (hotel_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN room.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN room.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trophy (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, criteria VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trophy_user (trophy_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(trophy_id, user_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7AAA1519F59AEEEF ON trophy_user (trophy_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7AAA1519A76ED395 ON trophy_user (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE badge_user ADD CONSTRAINT FK_299D3A50F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE badge_user ADD CONSTRAINT FK_299D3A50A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE35F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hotel ADD CONSTRAINT FK_3535ED98FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image ADD CONSTRAINT FK_C53D045F3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image ADD CONSTRAINT FK_C53D045F54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7C32323B FOREIGN KEY (negotiation_id_id) REFERENCES negotiation (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room ADD CONSTRAINT FK_729F519B9C905093 FOREIGN KEY (hotel_id_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trophy_user ADD CONSTRAINT FK_7AAA1519F59AEEEF FOREIGN KEY (trophy_id) REFERENCES trophy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trophy_user ADD CONSTRAINT FK_7AAA1519A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE badge_user DROP CONSTRAINT FK_299D3A50F7A2C2FC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE badge_user DROP CONSTRAINT FK_299D3A50A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE35F83FFC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hotel DROP CONSTRAINT FK_3535ED98FDDAB70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image DROP CONSTRAINT FK_C53D045F3243BB18
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE image DROP CONSTRAINT FK_C53D045F54177093
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307F7C32323B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refresh_token DROP CONSTRAINT FK_C74F2195A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room DROP CONSTRAINT FK_729F519B9C905093
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trophy_user DROP CONSTRAINT FK_7AAA1519F59AEEEF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trophy_user DROP CONSTRAINT FK_7AAA1519A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE badge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE badge_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hotel
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE negotiation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE refresh_token
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trophy
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trophy_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
    }
}
