<?php

namespace App\Application\Auth\UserToken\DTO;

final class RefreshTokenResult
{
    public function __construct(
        public string $raw,
        public ?\DateTimeImmutable $expiresAt,
    ) {
    }
}
