<?php

namespace App\Component\Security;

use App\Component\Exception\JwtException;
use JsonException;
use OpenSSLAsymmetricKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Psr\Log\LogLevel;

use function base64_decode;
use function str_repeat;
use function strlen;
use function strtr;

class JwtChecker
{
    private const RS256 = 'RS256';
    private ?Request $request = null;
    private string $payloadBase64Decode = '';

    /**
     * @param string $filePath
     * @param string|null $workingDir
     */
    public function __construct(private readonly string $filePath, private readonly ?string $workingDir)
    {
    }

    /**
     * @throws JwtException
     * @throws JsonException
     * @return JwtChecker|null
     */
    public function checkJwtToken(): ?JwtChecker
    {
        $credentials = $this->request->headers->get('Authorization');

        if (null === $credentials) {
            return null;
        }

        $accessTokenArray = explode(
            '.',
            str_replace('Bearer ', '', $credentials)
        );

        if (3 !== count($accessTokenArray)) {
            throw new JwtException(
                message: 'Некорректный ключ авторизации',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'WRONG_ACCESS_KEY'
            );
        }

        $header = json_decode(base64_decode($accessTokenArray[0]), false, 512, JSON_THROW_ON_ERROR);
        $payloadBase64Decode = (string) base64_decode($accessTokenArray[1]);
        $payloadJsonDecode = json_decode($payloadBase64Decode, false, 512, JSON_THROW_ON_ERROR);

        if (true === property_exists($payloadJsonDecode, 'exp') && $payloadJsonDecode->exp < time()) {
            throw new JwtException(
                message: 'Просроченный ключ авторизации',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'NEED_REFRESH_ACCESS_TOKEN',
                logLevel: LogLevel::INFO
            );
        }

        if (false === property_exists($header, 'alg') || self::RS256 !== $header->alg) {
            throw new JwtException(
                message: 'Неизвестный алгоритм шифрования ключа авторизации. Ожидается ' . self::RS256,
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'UNKNOWN_ENCRYPTION_ALGORITHM'
            );
        }

        if (
            !$this->checkSignature(
                $accessTokenArray[0],
                $accessTokenArray[1],
                $accessTokenArray[2]
            )
        ) {
            throw new JwtException(
                message: 'Невалидный токен',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'INVALID_TOKEN'
            );
        }

        $this->payloadBase64Decode = $payloadBase64Decode;

        return $this;
    }

    /**
     * Проверяет валидность подписи в JWT accessToken
     *
     * @param string $header
     * @param string $payload
     * @param string $signature
     * @throws JwtException
     * @return bool
     */
    private function checkSignature(string $header, string $payload, string $signature): bool
    {
        $pathPrefix = $this->workingDir ?? $_SERVER['PWD'] ?? $_SERVER['WORKING_DIR'];
        $key = openssl_pkey_get_public((string) file_get_contents($pathPrefix . $this->filePath));

        if (!($key instanceof OpenSSLAsymmetricKey)) {
            throw new JwtException(
                message: 'Проблемы с валидацией accessToken. Обратитесь в службу поддержки.',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'PUBLIC_KEY_ERROR'
            );
        }

        $result = openssl_verify(
            data: $header . '.' . $payload,
            signature: $this->urlSafeB64Decode($signature),
            public_key: $key,
            algorithm: OPENSSL_ALGO_SHA256
        );

        return 1 === $result;
    }

    /**
     * @param Request $request
     * @return JwtChecker
     */
    public function setRequest(Request $request): JwtChecker
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayloadBase64Decode(): string
    {
        return $this->payloadBase64Decode;
    }

    /**
     * import from firebase/php-jwt
     *
     * @param string $signature
     * @return string
     */
    private function urlSafeB64Decode(string $signature): string
    {
        $remainder = strlen($signature) % 4;

        if ($remainder) {
            $padLen = 4 - $remainder;
            $signature .= str_repeat('=', $padLen);
        }

        return (string) base64_decode(strtr($signature, '-_', '+/'));
    }
}
