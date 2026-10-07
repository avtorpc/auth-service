<?php

namespace App\Controller\Api\LoginPass;

use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Application\Auth\RefreshToken\Mapper\RefreshCommandMapper;
use App\Application\Auth\RefreshToken\Mapper\RefreshDomainMapper;
use App\Application\Auth\RefreshToken\Mapper\RefreshJsonMapper;
use App\Application\Auth\RefreshToken\RefreshTokenHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class RefreshTokenController
{
    public function __construct(
        private RefreshTokenHandler $handler,
    ) {}

    #[Route('/refresh', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * 1. JSON → RAW DTO
         */
        $raw = RefreshJsonMapper::fromJson(
            $request->getContent()
        );

        /**
         * 2. RAW → CLEAN DTO
         */
        $dto = RefreshDomainMapper::map($raw);

        /**
         * 3. CLEAN DTO → COMMAND
         */
        $command = RefreshCommandMapper::mapRequestToCommand(
            $dto,
            $request->getClientIp()
        );

        /**
         * 4. BUSINESS LOGIC CONTEXT
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
