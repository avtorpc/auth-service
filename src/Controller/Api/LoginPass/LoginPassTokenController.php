<?php

namespace App\Controller\Api\LoginPass;

use App\Application\Auth\UserToken\AuthLoginHandler;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Application\Auth\UserToken\Mapper\AuthJsonMapper;
use App\Application\Auth\UserToken\Mapper\AuthDomainMapper;
use App\Application\Auth\UserToken\Mapper\AuthCommandMapper;
use App\Application\Auth\UserToken\AuthHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Классическая схема входа Логин-Пароль
 */
class LoginPassTokenController
{
    public function __construct(
        private AuthLoginHandler $handler,
    ) {}

    #[Route('/sign-in', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * 1. JSON → RAW DTO (без типов, только extraction)
         */
        $raw = AuthJsonMapper::fromJson($request->getContent());

        /**
         * 2. RAW → CLEAN DTO (type casting + controlled errors)
         */
        $dto = AuthDomainMapper::map($raw);

        /**
         * 3. CLEAN DTO → COMMAND (твоя существующая логика)
         */
        $command = AuthCommandMapper::mapRequestToCommand(
            $dto,
            $request->getClientIp()
        );

        /**
         * 4. BUSINESS LOGIC
         */
        $context = new RequestContext(
            ip: $request->getClientIp(),
            uri: $request->getRequestUri(),
            method: $request->getMethod(),
            userAgent: $request->headers->get('User-Agent'),
        );

        $response = $this->handler->handle($command, $context);

        return new JsonResponse(
            $response->toArray()
        );
    }
}
