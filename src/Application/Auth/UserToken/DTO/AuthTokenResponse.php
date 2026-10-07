<?php

namespace App\Application\Auth\UserToken\DTO;

use App\Shared\Time\ClockInterface;

final class AuthTokenResponse
{
    public function __construct(
        private string $accessToken,
        private string $refreshToken,
        private int $expiresInSeconds,
        private ClockInterface $clock
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'timestamp' => $this->clock->nowFormatted(),
            'data' => [
                'accessToken' => $this->accessToken,
                'refreshToken' => $this->refreshToken,
                'expiresIn' => $this->expiresInSeconds,
                'tokenType' => 'Bearer',
            ]
        ];
    }
}
