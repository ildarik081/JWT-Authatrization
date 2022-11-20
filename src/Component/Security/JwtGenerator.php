<?php

namespace App\Component\Security;

use App\Component\Exception\JwtException;
use OpenSSLAsymmetricKey;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class JwtGenerator
{
    /**
     * @param string $filePath
     */
    public function __construct(public readonly string $filePath)
    {
    }

    /**
     * Возвращает сигнатуру JWT ключа, закодированную в base64
     *
     * @throws JwtException
     */
    public function getEncodedSignature(string $data): string
    {
        $signature = '';
        $privateKey = openssl_pkey_get_private((string) file_get_contents($this->filePath));

        if (!($privateKey instanceof OpenSSLAsymmetricKey)) {
            throw new JwtException(
                message: 'Не удалось получить приватный ключ',
                code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                responseCode: 'CANT_GET_PRIVATE_KEY',
                logLevel: LogLevel::ERROR
            );
        }

        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    /**
     * Возвращает новую связку ключей шифрования
     *
     * @return array
     * @throws JwtException
     */
    public function createNewKeyPair(): array
    {
        $newKeyPair = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        if (!$newKeyPair) {
            throw new JwtException(
                message: 'Не удалось собрать новую пару ключей',
                code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                responseCode: 'ERROR_CREATE_NEW_KEY',
                logLevel: LogLevel::ERROR
            );
        }

        openssl_pkey_export($newKeyPair, $privateKeyPem);

        $details = openssl_pkey_get_details($newKeyPair);

        $publicKeyPem = is_array($details) ? $details['key'] : '';

        return [$privateKeyPem, $publicKeyPem];
    }
}
