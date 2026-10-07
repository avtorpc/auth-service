<?php

namespace App\Application\Auth\SmsSignIn;

use App\Application\Auth\SmsSignIn\Command\SmsSignInCommand;
use App\Application\Auth\UserToken\DTO\AuthTokenResponse;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Application\Auth\UserToken\AuthAccessTokenService;
use App\Application\Auth\UserToken\RefreshTokenService;
use Psr\Log\LoggerInterface;
use App\Shared\Time\ClockInterface;

final class SmsSignInHandler
{
    public function __construct(
        private SmsChallengeService $challengeService,
        private AuthAccessTokenService $accessTokenService,
        private RefreshTokenService $refreshTokenService,
        private LoggerInterface $logger,
        private ClockInterface $clock,
    ) {}

    public function handle(
        SmsSignInCommand $command,
        RequestContext $context
    ): AuthTokenResponse {

        $this->logger->info('SMS sign-in started', [
            'challengeId' => $command->challengeId,
            'ip' => $context->ip,
        ]);

        /**
         * 1. validate + verify + resolve user in ONE step
         */
        $user = $this->challengeService->validateAndResolve(
            $command->challengeId,
            $command->code
        );

        /**
         * 2. issue tokens
         */
        $accessToken = $this->accessTokenService->createAccessToken($user);

        $refreshToken = $this->refreshTokenService->create(
            $user,
            $context->userAgent,
            $context->ip
        );

        $this->logger->info('SMS sign-in success', [
            'userId' => $user->getId(),
            'ip' => $context->ip,
        ]);

        return new AuthTokenResponse(
            accessToken: $accessToken,
            refreshToken: $refreshToken->raw,
            expiresInSeconds: $this->accessTokenService->getTtl(),
            clock: $this->clock
        );
    }
}
