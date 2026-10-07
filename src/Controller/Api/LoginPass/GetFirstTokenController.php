<?php

namespace App\Controller\Api\LoginPass;

use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Application\GetFirstToken\FirstTokenHandler;
use App\Application\GetFirstToken\GetFirstTokenHandler;
use App\Application\GetFirstToken\Mapper\GetFirstTokenCommandMapper;
use App\Application\GetFirstToken\Mapper\GetFirstTokenDomainMapper;
use App\Application\GetFirstToken\Mapper\GetFirstTokenJsonMapper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Публичный контроллер для получения первого токена после прохождения авторизации
 * Отпраялем пользователю сообщение о авторизации
 */
final class GetFirstTokenController
{
    public function __construct(
        private GetFirstTokenHandler $handler,
    ) {}

    #[Route('/token/first', name:"GetFirstTokenRoute", methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * 1. JSON → RAW DTO
         */
        $raw = GetFirstTokenJsonMapper::fromJson(
            $request->getContent()
        );

        /**
         * 2. RAW → CLEAN DOMAIN DTO
         */
        $dto = GetFirstTokenDomainMapper::map($raw);

        /**
         * 3. DOMAIN → COMMAND
         */
        $command = GetFirstTokenCommandMapper::mapRequestToCommand(
            $dto,
            $request->getClientIp()
        );

        /**
         * 4. REQUEST CONTEXT
         */
        $context = new RequestContext(
            ip: $request->getClientIp(),
            uri: $request->getRequestUri(),
            method: $request->getMethod(),
            userAgent: $request->headers->get('User-Agent'),
        );

        /**
         * 5. BUSINESS LOGIC
         */
        $response = $this->handler->handle(
            $command,
            $context
        );

        return new JsonResponse(
            $response->toArray()
        );
    }
}
