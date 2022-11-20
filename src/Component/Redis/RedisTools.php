<?php

namespace App\Component\Redis;

use DateTime;
use Predis\ClientInterface;
use Symfony\Component\Uid\Uuid;

class RedisTools
{
    /**
     * @param int $timeRefreshTokenSec срок жизни Refresh Token
     * @param int $timeAttemptsLoginSec Таймаут авторизации с некорректным паролем
     * @param ClientInterface $redisClient
     */
    public function __construct(
        private readonly int $timeRefreshTokenSec,
        private readonly int $timeAttemptsLoginSec,
        private readonly ClientInterface $redisClient
    ) {
    }

    /**
     * Записать сессию (sessionKeys:$refreshTokenUuid)
     *
     * @param integer $userId
     * @return string refresh token
     */
    public function setSessionKeys(int $userId): string
    {
        $redisData = json_encode(
            [
                'userId' => $userId,
                'dtCreate' => (new DateTime())->format('Y-m-d H:i:s')
            ],
            JSON_THROW_ON_ERROR
        );

        $refreshToken = Uuid::v4();
        $this->redisClient->setex('sessionKeys:' . $refreshToken, $this->timeRefreshTokenSec, $redisData);

        return $refreshToken;
    }

    /**
     * Записать попытки авторизации (countAttempts:$login)
     *
     * @param string $login
     * @param int $remainingAttempts
     * @return void
     */
    public function setCountAttempts(string $login, int $remainingAttempts): void
    {
        $this->redisClient->setex(
            'countAttempts:' . $login,
            $this->timeAttemptsLoginSec,
            (string) $remainingAttempts
        );
    }

    /**
     * Очистить редис от данных пользователя
     *
     * @param string $session
     * @param string $refreshToken
     * @param integer|null $userId
     * @return void
     */
    public function clearRedisData(string $session, string $refreshToken, ?int $userId = null): void
    {
        $allEntitySession = $this->redisClient->keys('*:' . $session);

        foreach ($allEntitySession as $entitySession) {
            $this->redisClient->del($entitySession);
        }

        if (null !== $userId) {
            $allEntityUserId = $this->redisClient->keys('*:' . $userId);

            foreach ($allEntityUserId as $entityUserId) {
                $this->redisClient->del($entityUserId);
            }
        }

        if (null !== $refreshToken) {
            $this->redisClient->del('sessionKeys:' . $refreshToken);
        }
    }
}
