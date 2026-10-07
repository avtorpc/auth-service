<?php

namespace App\Application\Auth\RefreshToken\Command;

final  class RefreshTokenCommand
{
    public function __construct(
        public string $refreshToken,
        public ?string $ipAddress,
    ) {}
}
