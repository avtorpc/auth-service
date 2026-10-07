<?php

namespace App\Application\Auth\UserToken;

use App\Application\Auth\UserToken\Command\LoginCommand;
use App\Application\Auth\UserToken\DTO\AuthTokenResponse;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Application\Auth\UserToken\Service\AuthService;
use App\Application\Auth\UserToken\AuthRequestService;
use App\Application\Auth\UserToken\AuthValidator;
use App\Shared\Time\ClockInterface;
use Psr\Log\LoggerInterface;

final class AuthLoginHandler
{
    public function __construct(
        private AuthValidator $validator,
        private AuthRequestService $authService,
        private LoggerInterface $logger,
        private ClockInterface $clock,
        private AuthAttemptValidator $authAttemptValidator
    ) {}

    /**
     * Выполнение бизнес логики логина и выдачи токенов
     */
    public function handle(LoginCommand $command, RequestContext $context): AuthTokenResponse
    {
        $this->logger->info('Auth login started', [
            'login' => $command->login,
        ]);

        // 1. Бизнес-валидация
        $this->validator->validate($command);

        $this->authAttemptValidator->assertAuthAllowed(
            $command,
            $context
        );


        // 2. Логика авторизации + выдача токенов
        $tokenPair = $this->authService->authenticate($command);

        $this->logger->info('Auth login success', [
            'login' => $command->login,
        ]);

        // 3. Ответ (JWT + refresh)
        return new AuthTokenResponse(
            accessToken: $tokenPair->accessToken,
            refreshToken: $tokenPair->refreshToken,
            expiresInSeconds: $tokenPair->expiresInSeconds,
            clock: $this->clock
        );
    }
}
