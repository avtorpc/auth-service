<?php

namespace App\Application\Auth\RefreshToken\DTO;

final class RefreshTokenRawDto
{
    public function __construct(
        public mixed $refreshToken,
    ) {}
}
