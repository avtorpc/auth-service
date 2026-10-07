<?php

namespace App\Application\Auth\RefreshToken;

use App\Application\Auth\RefreshToken\Command\RefreshTokenCommand;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class RefreshTokenValidator
{
    public function validate(RefreshTokenCommand $command): void
    {
        $token = $command->refreshToken;

        if (trim($token) === '') {
            throw new BadRequestException(
                'Refresh token is empty',
                ErrorCode::AUTH_FIELD_EMPTY
            );
        }

        if (!$this->isValidFormat($token)) {
            throw new BadRequestException(
                'Invalid refresh token format',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }
    }

    private function isValidFormat(string $token): bool
    {
        $token = trim($token);

        return ctype_xdigit($token)
            && strlen($token) >= 64
            && strlen($token) <= 128;
    }
}
