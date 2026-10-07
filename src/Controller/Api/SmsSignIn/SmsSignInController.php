<?php

namespace App\Controller\Api\SmsSignIn;

use App\Application\Auth\SmsSignIn\Mapper\SmsSignInJsonMapper;
use App\Application\Auth\SmsSignIn\Mapper\SmsSignInDomainMapper;
use App\Application\Auth\SmsSignIn\Mapper\SmsSignInCommandMapper;
use App\Application\Auth\SmsSignIn\SmsSignInHandler;
use App\Application\Auth\UserToken\DTO\RequestContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SmsSignInController
{
    public function __construct(
        private SmsSignInHandler $handler,
    ) {}

    #[Route('/sign-in/confirm', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * 1. JSON → RAW DTO
         */
        $raw = SmsSignInJsonMapper::fromJson(
            $request->getContent()
        );

        /**
         * 2. RAW → CLEAN DTO
         */
        $dto = SmsSignInDomainMapper::map($raw);

        /**
         * 3. CLEAN DTO → COMMAND
         */
        $command = SmsSignInCommandMapper::mapRequestToCommand(
            $dto,
            $request->getClientIp(),
            $request->headers->get('User-Agent')
        );

        /**
         * 4. CONTEXT
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
        $response = $this->handler->handle($command, $context);

        return new JsonResponse(
            $response->toArray()
        );
    }
}
