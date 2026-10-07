<?php

namespace App\Application\Auth\UserToken\Command;

final class LoginCommand
{
    public function __construct(
        public string $login,
        public string $password,
        public ?string $userAgent = null,
        public ?string $ipAddress = null,
    ) {}
}
