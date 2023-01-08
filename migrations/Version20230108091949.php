<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230108091949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(32) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) DEFAULT NULL, second_name VARCHAR(50) DEFAULT NULL, dt_birth DATE DEFAULT NULL, ip VARCHAR(15) NOT NULL, dt_create TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, dt_update TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, arx BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX user_emailx_arxx ON users (email, arx)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'Идентификатор пользователя\'');
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
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE users');
    }
}
