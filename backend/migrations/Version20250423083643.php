<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423083643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE ambiance (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE badge (id SERIAL NOT NULL, namename VARCHAR(255) NOT NULL, description TEXT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, room_id_id INT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, total_price NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE9D86650F ON booking (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE35F83FFC ON booking (room_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, hotel_id_id INT DEFAULT NULL, room_id_id INT DEFAULT NULL, added_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_68C58ED99D86650F ON favorite (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_68C58ED99C905093 ON favorite (hotel_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_68C58ED935F83FFC ON favorite (room_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hotel (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE matching (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, room_id_id INT DEFAULT NULL, type BOOLEAN NOT NULL, match_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DC10F2899D86650F ON matching (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DC10F28935F83FFC ON matching (room_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE offer (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, room_id_id INT DEFAULT NULL, proposed_price NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, offer_date DATE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29D6873E9D86650F ON offer (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29D6873E35F83FFC ON offer (room_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rating (id SERIAL NOT NULL, author_id_id INT DEFAULT NULL, room_id_id INT DEFAULT NULL, rating INT NOT NULL, comment TEXT NOT NULL, date DATE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D889262269CCBE9A ON rating (author_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D889262235F83FFC ON rating (room_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room (id SERIAL NOT NULL, hotel_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price_per_night NUMERIC(10, 2) NOT NULL, capacity INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_729F519B3243BB18 ON room (hotel_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room_service (room_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(room_id, service_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DBF263254177093 ON room_service (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DBF2632ED5CA9E6 ON room_service (service_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE room_ambiance (room_id INT NOT NULL, ambiance_id INT NOT NULL, PRIMARY KEY(room_id, ambiance_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5021F23F54177093 ON room_ambiance (room_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5021F23F37A05A93 ON room_ambiance (ambiance_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE search (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, criteria VARCHAR(255) NOT NULL, search_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B4F0DBA79D86650F ON search (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE service (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transaction (id SERIAL NOT NULL, booking_id_id INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_723705D1EE3863E2 ON transaction (booking_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_badge (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, badge_id_id INT DEFAULT NULL, awarded_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1C32B3459D86650F ON user_badge (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1C32B3451B8B387B ON user_badge (badge_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_preference (id SERIAL NOT NULL, user_id_id INT DEFAULT NULL, budget NUMERIC(10, 2) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FA0E76BF9D86650F ON user_preference (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_preference_ambiance (user_preference_id INT NOT NULL, ambiance_id INT NOT NULL, PRIMARY KEY(user_preference_id, ambiance_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7D3A1E2369E8F90 ON user_preference_ambiance (user_preference_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7D3A1E237A05A93 ON user_preference_ambiance (ambiance_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_preference_service (user_preference_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(user_preference_id, service_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2D357339369E8F90 ON user_preference_service (user_preference_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2D357339ED5CA9E6 ON user_preference_service (service_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE35F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED99D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED99C905093 FOREIGN KEY (hotel_id_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED935F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE matching ADD CONSTRAINT FK_DC10F2899D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE matching ADD CONSTRAINT FK_DC10F28935F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offer ADD CONSTRAINT FK_29D6873E9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offer ADD CONSTRAINT FK_29D6873E35F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating ADD CONSTRAINT FK_D889262269CCBE9A FOREIGN KEY (author_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating ADD CONSTRAINT FK_D889262235F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room ADD CONSTRAINT FK_729F519B3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_service ADD CONSTRAINT FK_DBF263254177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_service ADD CONSTRAINT FK_DBF2632ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_ambiance ADD CONSTRAINT FK_5021F23F54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_ambiance ADD CONSTRAINT FK_5021F23F37A05A93 FOREIGN KEY (ambiance_id) REFERENCES ambiance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE search ADD CONSTRAINT FK_B4F0DBA79D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD CONSTRAINT FK_723705D1EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES booking (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_badge ADD CONSTRAINT FK_1C32B3459D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_badge ADD CONSTRAINT FK_1C32B3451B8B387B FOREIGN KEY (badge_id_id) REFERENCES badge (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference ADD CONSTRAINT FK_FA0E76BF9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_ambiance ADD CONSTRAINT FK_C7D3A1E2369E8F90 FOREIGN KEY (user_preference_id) REFERENCES user_preference (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_ambiance ADD CONSTRAINT FK_C7D3A1E237A05A93 FOREIGN KEY (ambiance_id) REFERENCES ambiance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_service ADD CONSTRAINT FK_2D357339369E8F90 FOREIGN KEY (user_preference_id) REFERENCES user_preference (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_service ADD CONSTRAINT FK_2D357339ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD hotel_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6493243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D6493243BB18 ON "user" (hotel_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6493243BB18
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE35F83FFC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite DROP CONSTRAINT FK_68C58ED99D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite DROP CONSTRAINT FK_68C58ED99C905093
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite DROP CONSTRAINT FK_68C58ED935F83FFC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE matching DROP CONSTRAINT FK_DC10F2899D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE matching DROP CONSTRAINT FK_DC10F28935F83FFC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offer DROP CONSTRAINT FK_29D6873E9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE offer DROP CONSTRAINT FK_29D6873E35F83FFC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating DROP CONSTRAINT FK_D889262269CCBE9A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating DROP CONSTRAINT FK_D889262235F83FFC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room DROP CONSTRAINT FK_729F519B3243BB18
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_service DROP CONSTRAINT FK_DBF263254177093
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_service DROP CONSTRAINT FK_DBF2632ED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_ambiance DROP CONSTRAINT FK_5021F23F54177093
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE room_ambiance DROP CONSTRAINT FK_5021F23F37A05A93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE search DROP CONSTRAINT FK_B4F0DBA79D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP CONSTRAINT FK_723705D1EE3863E2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_badge DROP CONSTRAINT FK_1C32B3459D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_badge DROP CONSTRAINT FK_1C32B3451B8B387B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference DROP CONSTRAINT FK_FA0E76BF9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_ambiance DROP CONSTRAINT FK_C7D3A1E2369E8F90
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_ambiance DROP CONSTRAINT FK_C7D3A1E237A05A93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_service DROP CONSTRAINT FK_2D357339369E8F90
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preference_service DROP CONSTRAINT FK_2D357339ED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ambiance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE badge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hotel
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE matching
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE offer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rating
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room_service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE room_ambiance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE search
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transaction
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_badge
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_preference
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_preference_ambiance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_preference_service
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D6493243BB18
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP hotel_id
        SQL);
    }
}
