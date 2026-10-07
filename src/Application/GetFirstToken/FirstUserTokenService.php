<?php

namespace App\Application\GetFirstToken;

use App\Application\GetFirstToken\Command\GetFirstTokenCommand;
use App\Application\Auth\UserToken\AuthAccessTokenService;
use App\Application\Auth\UserToken\RefreshTokenService;
use App\Infrastructure\User\UserRepository;
use App\Domain\User\User;
use App\Shared\Exception\UserNotFoundException;


/**
 * Выдача токена
 * Пока применяется при первом входе после регистрационных проверок
 */
final class FirstUserTokenService
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthAccessTokenService $accessTokenService,
        private RefreshTokenService $refreshTokenService,
    ) {}

    public function issue(GetFirstTokenCommand $command, object $context): object
    {
        $user = $this->loadUser($command, $context);

        $accessToken = $this->accessTokenService->createAccessToken($user);

        $refreshToken = $this->refreshTokenService->create(
            $user,
            $context->userAgent ?? null,
            $context->ip ?? null
        );

        return (object) [
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken->raw,
            'expiresInSeconds' => $this->accessTokenService->getTtl(),
        ];
    }

    public function loadUser(GetFirstTokenCommand $command, object $context): User
    {
        $user = $this->userRepository->findByUuid($command->userUuid);

        if (!$user) {
            throw new UserNotFoundException(
                context: [
                    'user_uuid' => $command->userUuid,
                    'operation' => self::class . '::loadUser',
                    'service' => 'auth-service',
                    'correlation_id' => $context->correlationId ?? null,
                    'trace_id' => $context->traceId ?? null,
                    'user_agent' => $context->userAgent ?? null,
                    'ip' => $context->ip ?? null,
                    'occurred_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        return $user;
    }
}
