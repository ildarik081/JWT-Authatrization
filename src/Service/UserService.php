<?php

namespace App\Service;

use App\Component\Builder\UserBuilder;
use App\Component\Exception\BuilderException;
use App\Component\Exception\RepositoryException;
use App\Component\Exception\UserServiceException;
use App\Component\Factory\SimpleDtoFactory;
use App\Component\Redis\RedisTools;
use App\Dto\ControllerRequest\BaseDtoRequest;
use App\Dto\ControllerRequest\RefreshTokenDtoRequest;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Dto\ControllerResponse\SuccessDtoResponse;
use App\Dto\ControllerResponse\UserDtoResponse;
use App\Repository\UserRepository;
use Predis\ClientInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserService
{
    /**
     * @param ClientInterface $redisClient
     * @param UserRepository $userRepository
     * @param UserBuilder $builder
     * @param RedisTools $redisTools
     */
    public function __construct(
        private readonly ClientInterface $redisClient,
        private readonly UserRepository $userRepository,
        private readonly UserBuilder $builder,
        private readonly RedisTools $redisTools,
    ) {
    }

    /**
     * Получить информацию о пользователе
     *
     * @param BaseDtoRequest $request
     * @throws RepositoryException
     * @return UserDtoResponse
     */
    public function getProfile(BaseDtoRequest $request): UserDtoResponse
    {
        $user = $this->userRepository->getUser($request);

        return SimpleDtoFactory::createUserDtoResponse($user);
    }

    /**
     * Обновление данных пользователя
     *
     * @param UserDtoRequest $userDtoRequest
     * @throws BuilderException
     * @return UserDtoResponse
     */
    public function updateUser(UserDtoRequest $userDtoRequest): UserDtoResponse
    {
        $this->builder->setUserDto($userDtoRequest);

        if (null !== $userDtoRequest->jwtInfo) {
            $user = $this->userRepository->getUser($userDtoRequest);
            $this->builder->setUser($user);
        }

        $resultUser = $this->builder->build()->getResult();
        $this->userRepository->add($resultUser);

        return SimpleDtoFactory::createUserDtoResponse($resultUser);
    }

    /**
     * Удаление пользователя
     *
     * @param RefreshTokenDtoRequest $request
     * @throws UserServiceException
     * @throws BuilderException
     * @throws RepositoryException
     * @return SuccessDtoResponse
     */
    public function deleteUser(RefreshTokenDtoRequest $request): SuccessDtoResponse
    {
        $session = $this->redisClient->get('sessionKeys:' . $request->refreshToken);

        if (null === $session) {
            throw new UserServiceException(
                message: 'Сессия не найдена',
                code: ResponseAlias::HTTP_BAD_REQUEST,
                responseCode: 'SESSION_NOT_FOUND',
                logLevel: LogLevel::WARNING
            );
        }

        $user = $this->userRepository->getUser($request);
        $user->setArx(true);

        $this->redisTools->clearRedisData(
            $request->session,
            $request->refreshToken,
            $request->jwtInfo->user->id
        );

        $this->userRepository->add($user);

        return SimpleDtoFactory::createSuccessDtoResponse(true);
    }
}
