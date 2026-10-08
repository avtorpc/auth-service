<?php

namespace App\Application\Auth\UserToken;

use App\Domain\User\User;
use App\Shared\Time\ClockInterface;
use Firebase\JWT\JWT;

final class AuthAccessTokenService
{
    public function __construct(
        private readonly string $privateKey,
        private readonly int $ttl,
        private readonly string $issuer = 'auth-service',
        private readonly string $audience = 'api',
        private readonly ClockInterface $clock
    ) {}

    public function createAccessToken(User $user): string
    {
        $iat = $this->clock->nowTimestamp();

        $payload = [
            'sub' => (string) $user->getUUID(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),

            'iat' => $iat,
            'nbf' => $iat,
            'exp' => $iat + $this->ttl,

            'iss' => $this->issuer,
            'aud' => $this->audience,
        ];

        return JWT::encode(
            $payload,
            $this->privateKey,
            'RS256'
        );
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }
}
