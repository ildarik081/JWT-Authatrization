<?php

namespace App\Tests\Component\Utils;

use App\Component\Utils\DataChecker;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class DataCheckerTest extends TestCase
{
    public function testTryGetEmail()
    {
        $email = 'test@test.ru';
        $checkEmail = DataChecker::tryGetEmail($email);
        assertEquals('test@test.ru', $checkEmail);
    }
}
