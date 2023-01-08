<?php

namespace App\Component\Resolver;

use App\Component\Interface\AbstractDtoControllerRequest;
use App\Component\Exception\JwtException;
use App\Component\Exception\ParamsResolverException;
use App\Component\Security\JwtChecker;
use App\Dto\ControllerRequest\UserDtoRequest;
use App\Dto\JwtInfoDto;
use Generator;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Psr\Log\LogLevel;

class RequestDtoResolver implements ArgumentValueResolverInterface
{
    /** ключ для значения сессии */
    private const SESSION = 'session';

    /**
     * @param array $urlWithoutJwt
     * @param SerializerInterface $serializer
     * @param JwtChecker $jwtChecker
     */
    public function __construct(
        private readonly array $urlWithoutJwt,
        private readonly SerializerInterface $serializer,
        private readonly JwtChecker $jwtChecker
    ) {
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     *
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), AbstractDtoControllerRequest::class);
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @throws JwtException
     * @throws JsonException
     * @throws ParamsResolverException
     * @return Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if ($request->getSession()->has(self::SESSION)) {
            $session = $request->getSession()->get(self::SESSION, '');
        } else {
            $session = uniqid('', true);
            $request->getSession()->set(self::SESSION, $session);
        }

        $this->parsingUrl($request);

        $jwtInfo = $this->jwtChecker
            ->setRequest($request)
            ->checkJwtToken()
            ?->getPayloadBase64Decode();

        if (Request::METHOD_GET === $request->getMethod()) {
            $data = (string) json_encode($request->query->all(), JSON_THROW_ON_ERROR);
        } else {
            $data = (string) $request->getContent();
        }

        /** @var AbstractDtoControllerRequest $result */
        $result = $this->serializer->deserialize($data, $argument->getType(), 'json');
        $result->session = $session;

        if ('' !== $jwtInfo && null !== $jwtInfo) {
            $result->jwtInfo = $this->serializer->deserialize($jwtInfo, JwtInfoDto::class, 'json');
        }

        if ($result instanceof UserDtoRequest) {
            $result->ip = $request->getClientIp();
        }

        yield $result;
    }

    /**
     * Проверка URL без JWT
     *
     * @param Request $request
     * @throws ParamsResolverException
     * @return void
     */
    public function parsingUrl(Request $request): void
    {
        $credentials = $request->headers->get('Authorization');

        if (null === $credentials) {
            $checkWithoutJwt = false;
            $requestUri = $request->getRequestUri();
            $arrUrl = explode('?', $requestUri);
            $requestUri = $arrUrl[0];

            foreach ($this->urlWithoutJwt as $withoutJwt) {
                if ($withoutJwt === $requestUri) {
                    $checkWithoutJwt = true;
                }
            }

            if ($checkWithoutJwt) {
                return;
            }

            throw new ParamsResolverException(
                message: 'Требуется авторизация',
                code: ResponseAlias::HTTP_UNAUTHORIZED,
                responseCode: 'NO_AUTHORIZATION',
                logLevel: LogLevel::INFO
            );
        }
    }
}
