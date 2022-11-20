<?php

namespace App\Tests\Component\Utils;

use App\Component\StaticDataGenerator;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class StaticDataGeneratorTest extends TestCase
{
    public function testGetJwt()
    {
        $timeAccessToken = '+10 minutes';
        $filePath = $_ENV['JWT_SECRET_KEY_PATH'];
        $workingDir = $_ENV['WORKING_DIR'] ?? $_SERVER['WORKING_DIR'] ?? $_SERVER['PWD'];
        $staticDataGenerator = new StaticDataGenerator($timeAccessToken, $filePath, $workingDir);

        $user = new User();
        $user->setFirstName('Тест');
        $user->setFirstName('Тест');
        $user->setLastName('Тест');

        $jwt = $staticDataGenerator->getJwt($user);
        self::assertNotNull($jwt);
    }
}
