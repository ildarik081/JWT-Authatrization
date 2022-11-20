<?php

namespace App\Component;

use App\Component\Exception\ValidatorException;
use App\Component\Utils\DataChecker;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Repository\UserRepository;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DtoValidator
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * Валидировать входные данные UserDtoRequest
     *
     * @param UserDtoRequest $userDtoRequest
     * @param bool $update
     * @return void
     * @throws ValidatorException
     */
    public function validateUserDto(UserDtoRequest $userDtoRequest, bool $update = false): void
    {
        $errorMessages = [];

        if (null !== $userDtoRequest->email) {
            if (!DataChecker::tryGetEmail($userDtoRequest->email)) {
                $errorMessages[] = 'Некорректный email';
            }

            $checkEmail = $this->userRepository->findOneBy(['email' => $userDtoRequest->email]);

            if (null !== $checkEmail) {
                $errorMessages[] = "Email $userDtoRequest->email уже занят";
            }
        }

        if (!$update && null === $userDtoRequest->password) {
            $errorMessages[] = 'Заполните пароль';
        }

        if (null === $userDtoRequest->firstName) {
            $errorMessages[] = 'Заполните имя';
        }

        if (count($errorMessages) > 0) {
            throw new ValidatorException(
                message: implode('; ', $errorMessages),
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'VALIDATOR_USER_DTO_REQUEST',
                logLevel: LogLevel::INFO
            );
        }
    }
}
