<?php

namespace App\Controller\Api\SmsSignIn;

use App\Application\Auth\SmsSignIn\Mapper\RequestSmsCodeJsonMapper;
use App\Application\Auth\SmsSignIn\Mapper\RequestSmsCodeDomainMapper;
use App\Application\Auth\SmsSignIn\Mapper\RequestSmsCodeCommandMapper;
use App\Application\Auth\SmsSignIn\RequestSmsCodeHandler;
use App\Application\Auth\UserToken\DTO\RequestContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class RequestSmsCodeController
{
    public function __construct(
        private RequestSmsCodeHandler $handler,
    ) {
    }

    #[Route('/sign-in/request', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * 1. JSON → RAW DTO
         */
        $raw = RequestSmsCodeJsonMapper::fromJson(
            $request->getContent()
        );

        /**
         * 2. RAW → CLEAN DTO
         */
        $dto = RequestSmsCodeDomainMapper::map($raw);

        /**
         * 3. CLEAN DTO → COMMAND
         */
        $command = RequestSmsCodeCommandMapper::mapRequestToCommand(
            $dto,
            $request->getClientIp(),
            $request->headers->get('User-Agent')
        );

        /**
         * 4. CONTEXT (как у тебя в LoginPass)
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
