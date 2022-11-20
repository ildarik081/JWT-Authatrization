<?php

namespace App\Controller;

use App\Component\DtoValidator;
use App\Component\Exception\AutharizationServiceException;
use App\Component\Exception\AuthServiceException;
use App\Component\Exception\BuilderException;
use App\Component\Exception\JsonFactoryException;
use App\Component\Exception\RepositoryException;
use App\Component\Exception\StaticDataGeneratorException;
use App\Component\Exception\ValidatorException;
use App\Dto\ControllerRequest\LoginDtoRequest;
use App\Dto\ControllerRequest\RefreshTokenDtoRequest;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Dto\ControllerResponse\JwtDtoResponse;
use App\Dto\ControllerResponse\SuccessDtoResponse;
use App\Dto\ControllerResponse\UserJwtDtoResponse;
use App\Service\AutharizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;

class AutharizationController extends AbstractController
{
    /**
     * @param AutharizationService $authService
     */
    public function __construct(private readonly AutharizationService $authService)
    {
    }

    /**
     * Авторизация
     *
     * @Route("/api/login", name="login", methods={"POST"})
     * @OA\RequestBody(
     *    description="Логин/пароль",
     *    @Model(type=LoginDtoRequest::class)
     * )
     * @OA\Response(
     *      response=200,
     *      description="Возвращает access token и refresh token",
     *      @Model(type=JwtDtoResponse::class)
     * )
     * @OA\Tag(name="Autharization")
     * @param LoginDtoRequest $request
     * @return JwtDtoResponse
     * @throws AutharizationServiceException
     * @throws RepositoryException
     * @throws StaticDataGeneratorException
     */
    public function authorization(LoginDtoRequest $request): JwtDtoResponse
    {
        return $this->authService->authorization($request->login, $request->password);
    }

    /**
     * Регистрация
     *
     * @Route("/api/registration", name="registration", methods={"POST"})
     * @OA\RequestBody(
     *    description="Поля для заполнения",
     *    @Model(type=UserDtoRequest::class)
     * )
     * @OA\Response(
     *      response=200,
     *      description="Возвращает массив с данными нового пользователя, access token и refresh token",
     *      @Model(type=UserJwtDtoResponse::class)
     * )
     * @OA\Tag(name="Autharization")
     * @param UserDtoRequest $userDtoRequest
     * @param DtoValidator $validator
     * @return UserJwtDtoResponse
     * @throws BuilderException
     * @throws ValidatorException
     * @throws StaticDataGeneratorException
     */
    public function registration(UserDtoRequest $userDtoRequest, DtoValidator $validator): UserJwtDtoResponse
    {
        $validator->validateUserDto($userDtoRequest);

        return $this->authService->registration($userDtoRequest);
    }

    /**
     * Выход
     *
     * @Route("/api/logout", name="logout", methods={"POST"})
     * @OA\RequestBody(
     *    description="Выход из системы",
     *    @Model(type=RefreshTokenDtoRequest::class)
     * )
     * @OA\Response(
     *      response=200,
     *      description="Возвращает success = true/false",
     *      @Model(type=SuccessDtoResponse::class)
     * )
     * @OA\Tag(name="Autharization")
     * @param RefreshTokenDtoRequest $request
     * @return SuccessDtoResponse
     * @throws AutharizationServiceException
     */
    public function logout(RefreshTokenDtoRequest $request): SuccessDtoResponse
    {
        return $this->authService->logout($request);
    }

    /**
     * Обновляет AccessToken и RefreshToken
     *
     * @Route("/api/refresh", name="refresh_tokens", methods={"POST"})
     * @OA\RequestBody(
     *    description="Продление сессии",
     *    @Model(type=RefreshTokenDtoRequest::class)
     * )
     * @OA\Response(
     *      response=200,
     *      description="Возвращает свежий AccessToken и RefreshToken",
     *      @Model(type=JwtDtoResponse::class)
     * )
     * @OA\Tag(name="Autharization")
     * @param RefreshTokenDtoRequest $request
     * @return JwtDtoResponse
     * @throws AutharizationServiceException
     * @throws StaticDataGeneratorException
     */
    public function refreshTokens(RefreshTokenDtoRequest $request): JwtDtoResponse
    {
        return $this->authService->refreshTokens($request);
    }
}
