<?php

namespace App\Application\Auth\RefreshToken;

use App\Application\Auth\RefreshToken\Command\RefreshTokenCommand;
use App\Application\Auth\UserToken\DTO\AuthTokenResponse;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Shared\Time\ClockInterface;
use Psr\Log\LoggerInterface;

final class RefreshTokenHandler
{
    public function __construct(
        private RefreshTokenValidator $validator,
        private RefreshTokenService $service,
        private LoggerInterface $logger,
        private ClockInterface $clock,
    ) {}

    /**
     * Обновление access token по refresh token
     */
    public function handle(RefreshTokenCommand $command, RequestContext $context): AuthTokenResponse
    {
        $this->logger->info('Refresh token flow started', [
            'ip' => $context->ip,
        ]);

        // 1. Валидация refresh token (структура + подпись + blacklist)
        $this->validator->validate($command, $context);

        // 2. Ротация токенов
        $tokenPair = $this->service->refresh($command, $context);

        $this->logger->info('Refresh token success', [
            'ip' => $context->ip,
        ]);

        // 3. Ответ клиенту
        return new AuthTokenResponse(
            accessToken: $tokenPair->accessToken,
            refreshToken: $tokenPair->refreshToken,
            expiresInSeconds: $tokenPair->expiresInSeconds,
            clock: $this->clock
        );
    }
}
