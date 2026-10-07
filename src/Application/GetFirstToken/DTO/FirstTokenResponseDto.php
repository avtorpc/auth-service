<?php

namespace App\Application\GetFirstToken\DTO;

final class FirstTokenResponseDto
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
        public string $tokenType = 'Bearer',
    ) {}

    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
            'expiresIn' => $this->expiresIn,
            'tokenType' => $this->tokenType,
        ];
    }
}
