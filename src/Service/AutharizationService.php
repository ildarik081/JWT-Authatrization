<?php

namespace App\Service;

use JetBrains\PhpStorm\ArrayShape;
use App\Component\Builder\UserBuilder;
use App\Component\Exception\AutharizationServiceException;
use App\Component\Exception\RepositoryException;
use App\Component\StaticDataGenerator;
use App\Component\Exception\BuilderException;
use App\Component\Exception\StaticDataGeneratorException;
use App\Component\Factory\SimpleDtoFactory;
use App\Component\Redis\RedisTools;
use App\Component\Utils\DataChecker;
use App\Dto\ControllerRequest\RefreshTokenDtoRequest;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Dto\ControllerResponse\JwtDtoResponse;
use App\Dto\ControllerResponse\SuccessDtoResponse;
use App\Dto\ControllerResponse\UserJwtDtoResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Predis\ClientInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AutharizationService
{
    /**
     * @param UserBuilder $builder
     * @param ClientInterface $redisClient
     * @param UserRepository $userRepository
     * @param StaticDataGenerator $staticDataGenerator
     * @param RedisTools $redisTools
     * @param int $countAttemptsLogin количество попыток авторизации
     */
    public function __construct(
        private readonly UserBuilder $builder,
        private readonly ClientInterface $redisClient,
        private readonly UserRepository $userRepository,
        private readonly StaticDataGenerator $staticDataGenerator,
        private readonly RedisTools $redisTools,
        private readonly int $countAttemptsLogin
    ) {
    }

    /**
     * Авторизации
     *
     * @param string $login
     * @param string $password
     * @return JwtDtoResponse
     * @throws AutharizationServiceException
     * @throws StaticDataGeneratorException
     * @throws RepositoryException
     */
    public function authorization(string $login, string $password): JwtDtoResponse
    {
        $user = $this->checkDataUser($login, $password);
        $accessToket = $this->staticDataGenerator->getJwt($user);
        $refreshToken = $this->redisTools->setSessionKeys($user->getId());

        return SimpleDtoFactory::createJwtDtoResponse($accessToket, $refreshToken);
    }

    /**
     * Регистрация нового пользователя
     *
     * @param UserDtoRequest $userDtoRequest
     * @return UserJwtDtoResponse
     * @throws BuilderException
     * @throws StaticDataGeneratorException
     */
    public function registration(UserDtoRequest $userDtoRequest): UserJwtDtoResponse
    {
        $resultUser = $this->builder
            ->setUserDto($userDtoRequest)
            ->build()
            ->getResult();

        $this->userRepository->add($resultUser);

        $accessToket = $this->staticDataGenerator->getJwt($resultUser);
        $refreshToken = $this->redisTools->setSessionKeys($resultUser->getId());
        $jwtDto = SimpleDtoFactory::createJwtDtoResponse($accessToket, $refreshToken);

        return SimpleDtoFactory::createUserJwtDtoResponse($resultUser, $jwtDto);
    }

    /**
     * Выйти из системы
     *
     * @param RefreshTokenDtoRequest $request
     * @return SuccessDtoResponse
     * @throws AutharizationServiceException
     */
    public function logout(RefreshTokenDtoRequest $request): SuccessDtoResponse
    {
        $this->checkSession($request->refreshToken);

        $this->redisTools->clearRedisData(
            /** @phpstan-ignore-next-line */
            $request->session,
            $request->refreshToken,
            $request->jwtInfo?->user->id
        );

        return SimpleDtoFactory::createSuccessDtoResponse(true);
    }

    /**
     * Обновить пару токенов
     *
     * @param RefreshTokenDtoRequest $request
     * @return JwtDtoResponse
     * @throws AutharizationServiceException
     * @throws StaticDataGeneratorException
     * @throws RepositoryException
     */
    public function refreshTokens(RefreshTokenDtoRequest $request): JwtDtoResponse
    {
        $dataSession = $this->checkSession($request->refreshToken);
        $user = $this->userRepository->getUser($request);

        $accessToket = $this->staticDataGenerator->getJwt($user);
        $refreshToken = $this->redisTools->setSessionKeys($dataSession['userId']);
        $this->redisClient->del('sessionKeys:' . $refreshToken);

        return SimpleDtoFactory::createJwtDtoResponse($accessToket, $refreshToken);
    }

    /**
     * Проверить данные пользователя
     *
     * @param string $login
     * @param string $password
     * @return User
     * @throws AutharizationServiceException
     * @throws RepositoryException
     */
    private function checkDataUser(string $login, string $password): User
    {
        $user = $this->userRepository->findByEmail(
            DataChecker::tryGetEmail($login)
        );

        $countAttempts = $this->redisClient->get('countAttempts:' . $login);
        $remainingAttempts = null === $countAttempts
            ? $this->countAttemptsLogin
            : (int) $countAttempts;

        if ($remainingAttempts < 1) {
            throw new AutharizationServiceException(
                message: 'Превышено допустимое количество попыток. Попробуйте повторить авторизацию позже',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'EXCEEDED_ATTEMPTS_LOGIN',
                logLevel: LogLevel::WARNING
            );
        }

        if (false === $this->checkPassword($user, $password)) {
            --$remainingAttempts;
            $this->redisTools->setCountAttempts($login, $remainingAttempts);

            throw new AutharizationServiceException(
                message: "Неверный пароль! Осталось $remainingAttempts попыток",
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'INVALID_PASSWORD',
                logLevel: LogLevel::WARNING
            );
        }

        return $user;
    }

    /**
     * Проверка пароля
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    private function checkPassword(User $user, string $password): bool
    {
        return $user->getPassword() === md5($password);
    }

    /**
     * Проверка сессии
     *
     * @param string $refreshToken
     * @return array
     * @throws AutharizationServiceException
     */
    #[ArrayShape(
        [
            'userId' => 'int',
            'dtCreate' => 'string'
        ]
    )]
    private function checkSession(string $refreshToken): array
    {
        $dataRedis = $this->redisClient->get('sessionKeys:' . $refreshToken);

        if (null === $dataRedis) {
            throw new AutharizationServiceException(
                message: 'Сессия не найдена',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'SESSION_NOT_FOUND',
                logLevel: LogLevel::WARNING
            );
        }

        return json_decode($dataRedis, true, 512, JSON_THROW_ON_ERROR);
    }
}
