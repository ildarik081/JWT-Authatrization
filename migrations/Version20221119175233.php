<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119175233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(32) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) DEFAULT NULL, second_name VARCHAR(50) DEFAULT NULL, dt_birth DATE DEFAULT NULL, ip VARCHAR(15) NOT NULL, dt_create TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, dt_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, arx BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN users.email IS \'Email пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.password IS \'Хэш пароля\'');
        $this->addSql('COMMENT ON COLUMN users.first_name IS \'Имя пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.last_name IS \'Фамилия пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.second_name IS \'Отчество пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.dt_birth IS \'Дата рождения пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.ip IS \'Ip при регистрации\'');
        $this->addSql('COMMENT ON COLUMN users.dt_create IS \'Дата регистрации\'');
        $this->addSql('COMMENT ON COLUMN users.dt_update IS \'Дата обновления информации\'');
        $this->addSql('COMMENT ON COLUMN users.arx IS \'true - пользователь в архиве\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE users');
    }
}
