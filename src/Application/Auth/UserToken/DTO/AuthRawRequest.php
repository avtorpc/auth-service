<?php

namespace App\Application\Auth\UserToken\DTO;

final class AuthRawRequest
{
    public ?string $login = null;
    public ?string $password = null;
    public ?string $userAgent = null;
}
