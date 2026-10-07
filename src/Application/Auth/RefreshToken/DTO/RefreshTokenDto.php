<?php

namespace App\Application\Auth\RefreshToken\DTO;

final class RefreshTokenDto
{
    public function __construct(
        public string $refreshToken,
    ) {}
}
