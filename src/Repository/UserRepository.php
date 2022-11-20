<?php

namespace App\Repository;

use App\Component\Exception\RepositoryException;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Component\Interface\AbstractDtoControllerRequest;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Найти пользователя по email
     *
     * @param string|null $email
     * @return User
     * @throws RepositoryException
     */
    public function findByEmail(?string $email): User
    {
        if (null === $email) {
            throw new RepositoryException(
                message: 'Не верный email',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'BAD_EMAIL',
                logLevel: LogLevel::INFO
            );
        }

        $user = $this->findOneBy(
            [
                'email' => $email,
                'arx' => false
            ]
        );

        if (null === $user) {
            throw new RepositoryException(
                message: 'Пользователь не найден',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'USER_NOT_FOUND',
                logLevel: LogLevel::INFO
            );
        }

        return $user;
    }

    /**
     * Получить сущность пользователя
     *
     * @param AbstractDtoControllerRequest $request
     * @throws RepositoryException
     * @return User
     */
    public function getUser(AbstractDtoControllerRequest $request): User
    {
        $user = $this->findOneBy(['id' => $request->jwtInfo->user->id, 'arx' => false]);

        if (null === $user) {
            throw new RepositoryException(
                message: 'Пользователь не найден',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'USER_NOT_FOUND',
                logLevel: LogLevel::INFO
            );
        }

        return $user;
    }
}
