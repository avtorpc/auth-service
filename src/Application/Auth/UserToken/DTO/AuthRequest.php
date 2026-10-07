<?php

namespace App\Application\Auth\UserToken\DTO;

final class AuthRequest
{
    public function __construct(
        public string $login,
        public string $password,
        public ?string $userAgent,
    ) {}
}
