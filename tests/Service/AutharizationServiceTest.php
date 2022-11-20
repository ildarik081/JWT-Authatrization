<?php

namespace App\Tests\Service;

use App\Component\Builder\UserBuilder;
use App\Component\StaticDataGenerator;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Repository\UserRepository;
use App\Tests\AbstractTestCaseService;
use App\Dto\JwtInfoDto;
use App\Dto\UserDto;
use Symfony\Component\Uid\Uuid;
use App\Component\Redis\RedisTools;
use App\Dto\ControllerRequest\RefreshTokenDtoRequest;
use App\Service\AutharizationService;
use Predis\Client;

class AutharizationServiceTest extends AbstractTestCaseService
{
    public function testAuthorization()
    {
        $autharizationService = $this->getAutharizationService();

        $result = $autharizationService->authorization(
            '1test@test.ru',
            '1234'
        );

        self::assertNotNull($result->accessToken);
        self::assertNotNull($result->refreshToken);
    }

    public function testRegistration()
    {
        $autharizationService = $this->getAutharizationService();

        $userDtoRequest = new UserDtoRequest();
        $userDtoRequest->email = 'testRegistration@test.ru';
        $userDtoRequest->password = '1234';
        $userDtoRequest->firstName = 'ТестРегистрации';
        $userDtoRequest->dtBirth = '01.01.1990';
        $userDtoRequest->ip = '127.0.0.1';

        $result = $autharizationService->registration($userDtoRequest);

        self::assertEquals('testRegistration@test.ru', $result->user->email);
        self::assertEquals('ТестРегистрации', $result->user->firstName);
        self::assertEquals('01.01.1990', $result->user->dtBirth);

        self::assertNotNull($result->jwt->accessToken);
        self::assertNotNull($result->jwt->refreshToken);
    }

    public function testRefreshTokens()
    {
        $autharizationService = $this->getAutharizationService();

        $autharization = $autharizationService->authorization(
            '2test@test.ru',
            '1234'
        );

        $refreshTokenDtoRequest = new RefreshTokenDtoRequest();
        $refreshTokenDtoRequest->refreshToken = $autharization->refreshToken;

        $result = $autharizationService->refreshTokens($refreshTokenDtoRequest);

        $this->assertNotNull($result->accessToken);
        $this->assertNotNull($result->refreshToken);
    }

    public function testLogout()
    {
        $autharizationService = $this->getAutharizationService();

        $autharization = $autharizationService->authorization(
            '3test@test.ru',
            '1234'
        );

        $userDto = new UserDto();
        $userDto->id = 3;
        $jwtInfo = new JwtInfoDto();
        $jwtInfo->user = $userDto;
        $refreshTokenDtoRequest = new RefreshTokenDtoRequest();
        $refreshTokenDtoRequest->session = 'TestSession';
        $refreshTokenDtoRequest->jwtInfo = $jwtInfo;
        $refreshTokenDtoRequest->visitorUuid = Uuid::v4();
        $refreshTokenDtoRequest->refreshToken = $autharization->refreshToken;

        $result = $autharizationService->logout($refreshTokenDtoRequest);

        $this->assertEquals(true, $result->success);
    }

    private function getAutharizationService(): AutharizationService
    {
        $timeAccessToken = $_ENV['TIME_ACCESS_TOKEN'];
        $filePath = $_ENV['JWT_SECRET_KEY_PATH'];
        $countAttemptsLogin = $_ENV['COUNT_ATTEMPTS_LOGIN'];
        $timeAttemptsLoginSec = $_ENV['TIME_ATTEMPTS_LOGIN_SEC'];
        $timeRefreshTokenSec = $_ENV['TIME_REFRESH_TOKEN_SEC'];

        $workingDir = $_ENV['WORKING_DIR'] ?? $_SERVER['WORKING_DIR'] ?? $_SERVER['PWD'];
        $staticDataGenerator = new StaticDataGenerator($timeAccessToken, $filePath, $workingDir);
        $builder = new UserBuilder();

        $connectRedis = ['host' => $_ENV['REDIS_HOST'], 'port' => $_ENV['PREDIS_PORT']];
        $redisClient = new Client($connectRedis);

        $userRepository = self::getContainer()->get(UserRepository::class);

        $redisTools = new RedisTools(
            $timeRefreshTokenSec,
            $timeAttemptsLoginSec,
            $redisClient
        );

        $autharizationService = new AutharizationService(
            builder: $builder,
            redisClient: $redisClient,
            userRepository: $userRepository,
            staticDataGenerator: $staticDataGenerator,
            redisTools: $redisTools,
            countAttemptsLogin: $countAttemptsLogin
        );

        return $autharizationService;
    }
}
