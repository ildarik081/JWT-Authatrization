<?php

namespace App\Tests\Component\Builder;

use App\Component\Builder\UserBuilder;
use App\Component\StaticDataGenerator;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Dto\JwtInfoDto;
use App\Dto\UserDto;
use App\Entity\User;
use App\Tests\AbstractTestCaseService;

class UserBuilderTest extends AbstractTestCaseService
{
    public function testGetResult()
    {
        $builder = new UserBuilder();

        $userDto = new UserDto();
        $userDto->id = 6;
        $jwtInfo = new JwtInfoDto();
        $jwtInfo->user = $userDto;
        $userDtoRequest = new UserDtoRequest();
        $userDtoRequest->jwtInfo = $jwtInfo;
        $userDtoRequest->firstName = 'Имя';
        $userDtoRequest->lastName = 'Фамилия';
        $userDtoRequest->secondName = 'Отчество';
        $userDtoRequest->ip = '127.0.0.1';

        $user = $builder
            ->setUserDto($userDtoRequest)
            ->build()
            ->getResult();

        self::assertEquals('Имя', $user->getFirstName());
        self::assertEquals('Фамилия', $user->getLastName());
        self::assertEquals('Отчество', $user->getSecondName());
        self::assertEquals('127.0.0.1', $user->getIp());
    }

    public function testGetResultWithEmptyDto()
    {
        $builder = new UserBuilder();
        $userRepository = self::getContainer()->get(UserRepository::class);

        $userDto = new UserDto();
        $userDto->id = 7;
        $jwtInfo = new JwtInfoDto();
        $jwtInfo->user = $userDto;
        $userDtoRequest = new UserDtoRequest();
        $userDtoRequest->jwtInfo = $jwtInfo;
        $userDtoRequest->firstName = 'Имя';
        $userDtoRequest->lastName = 'Фамилия';
        $userDtoRequest->secondName = 'Отчество';
        $userDtoRequest->ip = '127.0.0.1';

        /** @var User $user */
        $user = $userRepository->findOneBy(['id' => 7]);

        $result = $builder
            ->setUser($user)
            ->setUserDto($userDtoRequest)
            ->build()
            ->getResult();

        self::assertEquals('Имя', $result->getFirstName());
        self::assertEquals('Фамилия', $result->getLastName());
        self::assertEquals('Отчество', $result->getSecondName());
        self::assertEquals('127.0.0.1', $result->getIp());
    }
}
