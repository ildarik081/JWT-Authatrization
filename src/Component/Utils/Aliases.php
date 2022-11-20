<?php

namespace App\Component\Utils;

class Aliases
{
    public const TYPE_JWT_ENCODE = 'RS256';
    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    /** Формат даты */
    public const D_FORMAT = 'd.m.Y';

    /** Формат даты и времени */
    public const DT_FORMAT = 'd.m.Y H:i:s';

    public const TEST_USER = [
        'firstName' => 'ТестовоеИмя',
        'lastName' => 'ТестоваяФимилия',
        'secondName' => 'ТестовоеОтчество',
        'email' => 'test@test.ru',
        'password' => '1234',
        'dtBirth' => '01.01.1990',
        'ip' => '127.0.0.1',
        'arx' => false
    ];
}
