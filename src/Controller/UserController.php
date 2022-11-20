<?php

namespace App\Controller;

use App\Component\DtoValidator;
use App\Component\Exception\BuilderException;
use App\Component\Exception\RepositoryException;
use App\Component\Exception\ValidatorException;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Dto\ControllerResponse\SuccessDtoResponse;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Dto\ControllerRequest\BaseDtoRequest;
use App\Dto\ControllerRequest\RefreshTokenDtoRequest;
use App\Dto\ControllerResponse\UserDtoResponse;

class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Данные о пользователе
     *
     * @Route("/api/user", name="get_user",  methods={"GET"})
     * @OA\Response(
     *      response=200,
     *      description="Возвращает данные о пользователе",
     *      @Model(type=UserDtoResponse::class)
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     * @param BaseDtoRequest $request
     * @return UserDtoResponse
     * @throws RepositoryException
     */
    public function getProfile(BaseDtoRequest $request): UserDtoResponse
    {
        return $this->userService->getProfile($request);
    }

    /**
     * Обновляет данные о пользователе
     *
     * @Route("/api/user", name="update_user", methods={"PUT"})
     * @OA\RequestBody(
     *    description="Существующие поля для обновления",
     *    @Model(type=UserDtoRequest::class)
     * )
     * @OA\Response(
     *      response=200,
     *      description="Обновляет данные о пользователе",
     *      @Model(type=UserDtoResponse::class)
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     * @param UserDtoRequest $userDtoRequest
     * @param DtoValidator $validator
     * @throws BuilderException
     * @throws ValidatorException
     * @return UserDtoResponse
     */
    public function updateUser(UserDtoRequest $userDtoRequest, DtoValidator $validator): UserDtoResponse
    {
        $validator->validateUserDto($userDtoRequest, true);

        return $this->userService->updateUser($userDtoRequest);
    }

    /**
     * Удаляет данные пользователя
     *
     * @Route("/api/user", name="delete_user", methods={"DELETE"})
     * @OA\RequestBody(
     *    description="Refresh token",
     *    @Model(type=RefreshTokenDtoRequest::class)
     * )
     * @OA\Response(
     *      response=200,
     *      description="Переводит учетную запись пользователя в архив",
     *      @Model(type=SuccessDtoResponse::class)
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     * @param RefreshTokenDtoRequest $request
     * @throws BuilderException
     * @throws RepositoryException
     * @return SuccessDtoResponse
     */
    public function deleteUser(RefreshTokenDtoRequest $request): SuccessDtoResponse
    {
        return $this->userService->deleteUser($request);
    }
}
