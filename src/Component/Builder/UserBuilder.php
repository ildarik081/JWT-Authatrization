<?php

namespace App\Component\Builder;

use App\Component\Builder\BuilderInterface;
use App\Component\Exception\BuilderException;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Entity\User;
use DateTime;
use Exception;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserBuilder implements BuilderInterface
{
    private ?User $resultUser = null;
    private ?User $user = null;
    private ?UserDtoRequest $userDtoRequest = null;

    /**
     * @return UserBuilder
     */
    public function build(): UserBuilder
    {
        if (null !== $this->user) {
            $this->resultUser = $this->user;
        } else {
            $this->resultUser = new User();
        }

        $this->setFirstName();
        $this->setLastName();
        $this->setSecondName();
        $this->setDtBirth();
        $this->setPassword();
        $this->setEmail();
        $this->setIp();

        return $this;
    }

    /**
     * @return UserBuilder
     */
    public function reset(): UserBuilder
    {
        $this->resultUser = null;
        $this->user = null;
        $this->userDtoRequest = null;

        return $this;
    }

    /**
     * @throws BuilderException
     * @return User
     */
    public function getResult(): User
    {
        if (null === $this->resultUser) {
            throw new BuilderException(
                message: 'Отсутствует метод build()',
                code: ResponseAlias::HTTP_BAD_REQUEST,
                responseCode: 'NOT_BUILD_METHOD',
                logLevel: LogLevel::ERROR
            );
        }

        $result = $this->resultUser;
        $this->reset();

        return $result;
    }

    /**
     * @param User $user
     * @return UserBuilder
     */
    public function setUser(User $user): UserBuilder
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param UserDtoRequest $request
     * @return UserBuilder
     */
    public function setUserDto(UserDtoRequest $request): UserBuilder
    {
        $this->userDtoRequest = $request;

        return $this;
    }

    private function setEmail(): void
    {
        if (null !== $this->userDtoRequest->email) {
            $this->resultUser->setEmail($this->userDtoRequest->email);
        }
    }

    private function setPassword(): void
    {
        if (null !== $this->userDtoRequest->password) {
            $this->resultUser->setPassword(md5($this->userDtoRequest->password));
        }
    }

    private function setFirstName(): void
    {
        if (null !== $this->userDtoRequest->firstName) {
            $this->resultUser->setFirstName(
                ucwords(strtolower($this->userDtoRequest->firstName))
            );
        }
    }

    private function setLastName(): void
    {
        if (null !== $this->userDtoRequest->lastName) {
            $this->resultUser->setLastName(
                ucwords(strtolower($this->userDtoRequest->lastName))
            );
        }
    }

    private function setSecondName(): void
    {
        if (null !== $this->userDtoRequest->secondName) {
            $this->resultUser->setSecondName(
                ucwords(strtolower($this->userDtoRequest->secondName))
            );
        }
    }

    /**
     * @throws BuilderException
     */
    private function setDtBirth(): void
    {
        try {
            if (null !== $this->userDtoRequest->dtBirth) {
                $this->resultUser->setDtBirth(new DateTime($this->userDtoRequest->dtBirth));
            }
        } catch (Exception $exception) {
            throw new BuilderException(
                message: 'DateTime Exception ' . $exception->getMessage(),
                code: ResponseAlias::HTTP_BAD_REQUEST,
                responseCode: 'DATETIME_EXCEPTION',
                logLevel: LogLevel::INFO
            );
        }
    }

    private function setIp(): void
    {
        if (null !== $this->userDtoRequest->ip) {
            $this->resultUser->setIp($this->userDtoRequest->ip);
        }
    }
}
