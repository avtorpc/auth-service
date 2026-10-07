<?php

namespace App\Application\Auth\RefreshToken;

use App\Application\Auth\RefreshToken\Command\RefreshTokenCommand;
use App\Application\Auth\UserToken\AuthAccessTokenService;
use App\Application\Auth\UserToken\DTO\RefreshTokenResult;
use App\Infrastructure\Auth\DbRefreshTokenRepository;
use App\Infrastructure\User\UserRepository;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;
use App\Shared\Time\ClockInterface;

final class RefreshTokenService
{
    public function __construct(
        private DbRefreshTokenRepository $refreshTokenRepository,
        private UserRepository $userRepository,
        private AuthAccessTokenService $accessTokenService,
        private ClockInterface $clock,
    ) {}

    public function refresh(RefreshTokenCommand $command, $context): object
    {
        $rawToken = $command->refreshToken;

        /**
         * 1. hash incoming token
         */
        $tokenHash = hash('sha256', $rawToken);

        /**
         * 2. find refresh session
         */
        $session = $this->refreshTokenRepository->findByHash($tokenHash);

        if (!$session) {
            throw new BadRequestException(
                'Invalid refresh token',
                ErrorCode::AUTH_INVALID_REFRESH_TOKEN
            );
        }

        /**
         * 3. validate expiry
         */
        $expiresAt = new \DateTimeImmutable(
            $session['expires_at'],
            new \DateTimeZone('UTC')
        );

        $now = $this->clock->now(); // уже UTC

        if ($expiresAt < $now) {
            throw new BadRequestException(
                'Refresh token expired',
                ErrorCode::AUTH_TOKEN_EXPIRED
            );
        }

        if (!empty($session['revoked_at'])) {
            throw new BadRequestException(
                'Refresh token revoked',
                ErrorCode::AUTH_TOKEN_REVOKED
            );
        }

        /**
         * 4. load user
         */
        $user = $this->userRepository->findById($session['user_id']);

        if (!$user) {
            throw new BadRequestException(
                'User not found',
                ErrorCode::AUTH_USER_NOT_FOUND
            );
        }

        /**
         * 5. generate new access token
         */
        $accessToken = $this->accessTokenService->createAccessToken($user);

        /**
         * 6. rotate refresh token
         */
        $newRefreshToken = bin2hex(random_bytes(64));
        $newHash = hash('sha256', $newRefreshToken);

        $this->refreshTokenRepository->rotate(
            userId: $user->getId(),
            oldHash: $tokenHash,
            newHash: $newHash,
            expiresAt: (clone $this->clock->now())->modify('+30 days'),
        );

        /**
         * 7. Response DTO (still simple stdClass for now)
         */
        return (object)[
            'accessToken' => $accessToken,
            'refreshToken' => $newRefreshToken,
            'expiresInSeconds' => $this->accessTokenService->getTtl(),
        ];
    }
}
