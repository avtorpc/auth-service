<?php

namespace App\Application\Auth\RefreshToken\Mapper;

use App\Application\Auth\RefreshToken\Command\RefreshTokenCommand;
use App\Application\Auth\RefreshToken\DTO\RefreshTokenDto;

final class RefreshCommandMapper
{
    public static function mapRequestToCommand(
        RefreshTokenDto $request,
        ?string $ipAddress = null
    ): RefreshTokenCommand {
        return new RefreshTokenCommand(
            refreshToken: $request->refreshToken,
            ipAddress: $ipAddress
        );
    }
}
