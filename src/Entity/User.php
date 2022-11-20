<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Component\Interface\Controller\ControllerResponseInterface;
use App\Component\Utils\Aliases;
use App\Repository\UserRepository;
use DateTime;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User implements ControllerResponseInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    /** @phpstan-ignore-next-line */
    private int $id;

    #[ORM\Column(
        type: 'string',
        length: 100,
        nullable: false,
        options: ['comment' => 'Email пользователя']
    )
    ]
    private string $email;

    #[ORM\Column(
        type: 'string',
        length: 32,
        nullable: false,
        options: ['comment' => 'Хэш пароля']
    )
    ]
    private string $password;

    #[ORM\Column(
        type: 'string',
        length: 50,
        nullable: false,
        options: ['comment' => 'Имя пользователя']
    )
    ]
    private string $firstName;

    #[ORM\Column(
        type: 'string',
        length: 50,
        nullable: true,
        options: ['comment' => 'Фамилия пользователя']
    )
    ]
    private ?string $lastName = null;

    #[ORM\Column(
        type: 'string',
        length: 50,
        nullable: true,
        options: ['comment' => 'Отчество пользователя']
    )
    ]
    private ?string $secondName = null;

    #[ORM\Column(
        type: 'date',
        nullable: true,
        options: ['comment' => 'Дата рождения пользователя']
    )
    ]
    private ?DateTime $dtBirth = null;

    #[ORM\Column(
        type: 'string',
        length: 15,
        nullable: false,
        options: ['comment' => 'Ip при регистрации']
    )
    ]
    private string $ip;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
        options: ['comment' => 'Дата регистрации']
    )
    ]
    private ?DateTime $dtCreate = null;

    #[ORM\Column(
        type: 'datetime',
        nullable: true,
        options: ['comment' => 'Дата обновления информации']
    )
    ]
    private ?DateTime $dtUpdate = null;

    #[ORM\Column(
        type: 'boolean',
        nullable: false,
        options: ['comment' => 'true - пользователь в архиве']
    )
    ]
    private bool $arx = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
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

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getDtCreate(): string
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
