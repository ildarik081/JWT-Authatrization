<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Component\Interface\Controller\ControllerResponseInterface;
use App\Component\Utils\Aliases;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\Index(name: 'user_emailx_arxx', columns: ['email', 'arx'])]
#[ORM\HasLifecycleCallbacks]
class User implements ControllerResponseInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[
        ORM\Column(
            type: Types::INTEGER,
            nullable: false,
            options: ['comment' => 'Идентификатор пользователя']
        )
    ]
    private ?int $id = null;

    #[
        ORM\Column(
            type: Types::STRING,
            length: 100,
            nullable: false,
            options: ['comment' => 'Email пользователя']
        )
    ]
    private ?string $email = null;

    #[
        ORM\Column(
            type: Types::STRING,
            length: 32,
            nullable: false,
            options: ['comment' => 'Хэш пароля']
        )
    ]
    private ?string $password = null;

    #[
        ORM\Column(
            type: Types::STRING,
            length: 50,
            nullable: false,
            options: ['comment' => 'Имя пользователя']
        )
    ]
    private ?string $firstName = null;

    #[
        ORM\Column(
            type: Types::STRING,
            length: 50,
            nullable: true,
            options: ['comment' => 'Фамилия пользователя']
        )
    ]
    private ?string $lastName = null;

    #[
        ORM\Column(
            type: Types::STRING,
            length: 50,
            nullable: true,
            options: ['comment' => 'Отчество пользователя']
        )
    ]
    private ?string $secondName = null;

    #[
        ORM\Column(
            type: Types::DATE_MUTABLE,
            nullable: true,
            options: ['comment' => 'Дата рождения пользователя']
        )
    ]
    private ?DateTime $dtBirth = null;

    #[
        ORM\Column(
            type: Types::STRING,
            length: 15,
            nullable: false,
            options: ['comment' => 'Ip при регистрации']
        )
    ]
    private ?string $ip = null;

    #[
        ORM\Column(
            type: Types::DATETIME_MUTABLE,
            nullable: false,
            options: ['comment' => 'Дата регистрации']
        )
    ]
    private ?DateTime $dtCreate = null;

    #[
        ORM\Column(
            type: Types::DATETIME_MUTABLE,
            nullable: false,
            options: ['comment' => 'Дата обновления информации']
        )
    ]
    private ?DateTime $dtUpdate = null;

    #[
        ORM\Column(
            type: Types::BOOLEAN,
            nullable: false,
            options: [
                'comment' => 'true - пользователь в архиве',
                'default' => false
            ]
        )
    ]
    private bool $arx = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(?string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function getDtBirth(): ?DateTime
    {
        return $this->dtBirth;
    }

    public function setDtBirth(?DateTime $dtBirth): self
    {
        $this->dtBirth = $dtBirth;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getDtCreate(): ?string
    {
        return ($this->dtCreate)->format(Aliases::DT_FORMAT);
    }

    #[ORM\PrePersist]
    public function setDtCreate(): self
    {
        $this->dtCreate = new DateTime();

        return $this;
    }

    public function getDtUpdate(): string
    {
        return ($this->dtUpdate)->format(Aliases::DT_FORMAT);
    }

    #[ORM\PreFlush]
    public function setDtUpdate(): self
    {
        $this->dtUpdate = new DateTime();

        return $this;
    }

    public function getArx(): bool
    {
        return $this->arx;
    }

    public function setArx(bool $arx): self
    {
        $this->arx = $arx;

        return $this;
    }
}
