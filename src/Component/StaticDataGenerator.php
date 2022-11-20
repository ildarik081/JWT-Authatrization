<?php

namespace App\Component;

use App\Component\Exception\StaticDataGeneratorException;
use App\Component\Utils\Aliases;
use App\Entity\User;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StaticDataGenerator
{
    /**
     * @param string $timeAccessToken cрок жизни access токена
     * @param string $filePath путь к секретному ключу
     * @param string|null $workingDir
     */
    public function __construct(
        private readonly string $timeAccessToken,
        private readonly string $filePath,
        private readonly ?string $workingDir
    ) {
    }

    /**
     * Получить JWT для пользователя
     *
     * @param User $user
     * @throws StaticDataGeneratorException
     * @return string access token
     */
    public function getJwt(User $user): string
    {
        $payload = [
            'user' => [
                'id' => $user->getId(),
                'ip' => $user->getIp(),
                'dtCreate' => $user->getDtCreate()
            ],
            'exp'  => (new DateTime())->modify($this->timeAccessToken)->getTimestamp()
        ];

        $pathPrefix = $this->workingDir ?? $_SERVER['PWD'] ?? $_SERVER['WORKING_DIR'];

        $filePath = file_get_contents($pathPrefix . $this->filePath) ? : '';

        try {
            $accesstoken = JWT::encode($payload, $filePath, Aliases::TYPE_JWT_ENCODE);
        } catch (Exception $exception) {
            throw new StaticDataGeneratorException(
                message: 'Ошибка генерации JWT (' . $exception->getMessage() . ')',
                code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                responseCode: 'ERROR_CREATE_JWT',
                logLevel: LogLevel::CRITICAL
            );
        }

        return $accesstoken;
    }
}
