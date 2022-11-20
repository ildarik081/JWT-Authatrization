<?php

namespace App\Component\Factory;

use App\Component\Utils\Aliases;
use App\Dto\ControllerResponse\JwtDtoResponse;
use App\Dto\ControllerResponse\SuccessDtoResponse;
use App\Dto\ControllerResponse\UserDtoResponse;
use App\Dto\ControllerResponse\UserJwtDtoResponse;
use App\Entity\User;

class SimpleDtoFactory
{
    /**
     * @param User $user
     * @return UserDtoResponse
     */
    public static function createUserDtoResponse(User $user): UserDtoResponse
    {
        $response = new UserDtoResponse();
        $response->id = $user->getId();
        $response->email = $user->getEmail();
        $response->firstName = $user->getFirstName();
        $response->lastName = $user->getLastName();
        $response->secondName = $user->getSecondName();
        $response->dtBirth =  null !== $user->getDtBirth()
            ? ($user->getDtBirth())->format(Aliases::D_FORMAT)
            : null;
        $response->ip = $user->getIp();

        return $response;
    }

    /**
     * @param bool $success
     * @return SuccessDtoResponse
     */
    public static function createSuccessDtoResponse(bool $success): SuccessDtoResponse
    {
        $response = new SuccessDtoResponse();
        $response->success = $success;

        return $response;
    }

    /**
     * @param string $accessToken
     * @param string $refreshToken
     * @return JwtDtoResponse
     */
    public static function createJwtDtoResponse(string $accessToken, string $refreshToken): JwtDtoResponse
    {
        $response = new JwtDtoResponse();
        $response->accessToken = $accessToken;
        $response->refreshToken = $refreshToken;

        return $response;
    }

    /**
     * @param User $user
     * @param JwtDtoResponse $jwtDtoResponse
     * @return UserJwtDtoResponse
     */
    public static function createUserJwtDtoResponse(User $user, JwtDtoResponse $jwtDtoResponse): UserJwtDtoResponse
    {
        $response = new UserJwtDtoResponse();
        $response->user = self::createUserDtoResponse($user);
        $response->jwt = $jwtDtoResponse;

        return $response;
    }
}
