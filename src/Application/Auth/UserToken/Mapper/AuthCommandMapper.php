<?php

namespace App\Application\Auth\UserToken\Mapper;

use App\Application\Auth\UserToken\DTO\AuthRequest;
use App\Application\Auth\UserToken\Command\LoginCommand;

final class AuthCommandMapper
{
    public static function mapRequestToCommand(
        AuthRequest $request,
        ?string $ipAddress = null
    ): LoginCommand {
        return new LoginCommand(
            login: $request->login,
            password: $request->password,
            userAgent: $request->userAgent,
            ipAddress: $ipAddress
        );
    }
}
