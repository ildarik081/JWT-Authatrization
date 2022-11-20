<?php

namespace App\Tests\Service;

use App\Component\Builder\UserBuilder;
use App\Component\Redis\RedisTools;
use App\Component\StaticDataGenerator;
use App\Dto\ControllerRequest\BaseDtoRequest;
use App\Dto\ControllerRequest\RefreshTokenDtoRequest;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Tests\AbstractTestCaseService;
use Symfony\Component\Uid\Uuid;
use App\Dto\JwtInfoDto;
use App\Dto\UserDto;
use Predis\Client;

class UserServiceTest extends AbstractTestCaseService
{
    public Client $redisClient;

    public function testGetProfile()
    {
        $userService = $this->getUserService();

        $userDto = new UserDto();
        $userDto->id = 5;
        $jwtInfo = new JwtInfoDto();
        $jwtInfo->user = $userDto;
        $baseDtoRequest = new BaseDtoRequest();
        $baseDtoRequest->jwtInfo = $jwtInfo;

        $result = $userService->getProfile($baseDtoRequest);

        self::assertEquals(5, $result->id);
        self::assertEquals('5test@test.ru', $result->email);
    }

    public function testUpdateUser()
    {
        $userService = $this->getUserService();

        $userDto = new UserDto();
        $userDto->id = 5;
        $jwtInfo = new JwtInfoDto();
        $jwtInfo->user = $userDto;
        $userDtoRequest = new UserDtoRequest();
        $userDtoRequest->jwtInfo = $jwtInfo;
        $userDtoRequest->firstName = 'Имя';
        $userDtoRequest->lastName = 'Фамилия';
        $userDtoRequest->secondName = 'Отчество';
        $userDtoRequest->ip = '127.0.0.1';

        $result = $userService->updateUser($userDtoRequest);
        self::assertEquals('Имя', $result->firstName);
        self::assertEquals('Фамилия', $result->lastName);
        self::assertEquals('Отчество', $result->secondName);
    }

    public function testDeleteUser()
    {
        $userService = $this->getUserService();

        $userDto = new UserDto();
        $userDto->id = 6;
        $jwtInfo = new JwtInfoDto();
        $jwtInfo->user = $userDto;
        $refreshTokenDtoRequest = new RefreshTokenDtoRequest();
        $refreshTokenDtoRequest->jwtInfo = $jwtInfo;
        $refreshTokenDtoRequest->refreshToken = $refreshToken = Uuid::v4();

        $this->redisClient->setex('sessionKeys:' . $refreshToken, 10, 'test');

        $userService->deleteUser($refreshTokenDtoRequest);

        $userRepository = self::getContainer()->get(UserRepository::class);
        $userDelete = $userRepository->findOneBy(['id' => 6, 'arx' => true]);

        self::assertEquals(1, count($userDelete));
    }

    private function getUserService(): UserService
    {
        $timeAttemptsLoginSec = $_ENV['TIME_ATTEMPTS_LOGIN_SEC'];
        $timeRefreshTokenSec = $_ENV['TIME_REFRESH_TOKEN_SEC'];

        $connectRedis = ['host' => $_ENV['REDIS_HOST'], 'port' => $_ENV['PREDIS_PORT']];
        $this->redisClient = new Client($connectRedis);

        $userRepository = self::getContainer()->get(UserRepository::class);

        $builder = new UserBuilder();

        $redisTools = new RedisTools(
            $timeRefreshTokenSec,
            $timeAttemptsLoginSec,
            $this->redisClient
        );

        $userService = new UserService(
            redisClient: $this->redisClient,
            userRepository: $userRepository,
            builder: $builder,
            redisTools: $redisTools,
        );

        return $userService;
    }
}
