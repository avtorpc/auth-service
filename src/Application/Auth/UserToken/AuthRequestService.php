<?php

namespace App\Application\Auth\UserToken;

use App\Application\Auth\UserToken\Command\LoginCommand;
use App\Application\Auth\UserToken\RefreshTokenService;
use App\Infrastructure\User\UserRepository;
use App\Shared\Exception\UnauthorizedException;

final class AuthRequestService
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthAccessTokenService $accessTokenService,
        private RefreshTokenService $refreshTokenService,
    ) {}

    public function authenticate(LoginCommand $command): object
    {
        // 1. Load user
        $user = $this->userRepository->findByEmail($command->login);

        if (!$user) {
            throw new UnauthorizedException('Invalid credentials');
        }

        // 2. Password check (domain responsibility)
        if (!$user->verifyPassword($command->password)) {
            throw new UnauthorizedException('Invalid credentials');
        }

        // 3. Issue access token (RS256 inside service)
        $accessToken = $this->accessTokenService->createAccessToken($user);

        // 4. Issue refresh token (stateful)
        $refreshToken = $this->refreshTokenService->create(
            $user,
            $command->userAgent,
            $command->ipAddress
        );

        // 5. Response DTO (still simple stdClass for now)
        return (object)[
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken->raw,
            'expiresInSeconds' => $this->accessTokenService->getTtl(),
        ];
    }
}
