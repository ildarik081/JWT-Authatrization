<?php

namespace App\DataFixtures;

use App\Component\Utils\Aliases;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Фикстуры для тестов
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setFirstName(Aliases::TEST_USER['firstName'] . $i);
            $user->setLastName(Aliases::TEST_USER['lastName'] . $i);
            $user->setSecondName(Aliases::TEST_USER['secondName'] . $i);
            $user->setEmail($i . Aliases::TEST_USER['email']);
            $user->setPassword(md5(Aliases::TEST_USER['password']));
            $user->setIp(Aliases::TEST_USER['ip']);
            $user->setArx(Aliases::TEST_USER['arx']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
