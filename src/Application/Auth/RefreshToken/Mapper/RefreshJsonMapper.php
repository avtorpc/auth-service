<?php

namespace App\Application\Auth\RefreshToken\Mapper;

use App\Application\Auth\RefreshToken\DTO\RefreshTokenRawDto;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class RefreshJsonMapper
{
    public static function fromJson(string $json): RefreshTokenRawDto
    {
        try {
            $data = json_decode(
                $json,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new BadRequestException(
                'Invalid JSON payload',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        return new RefreshTokenRawDto(
            refreshToken: $data['refreshToken'] ?? null,
        );
    }
}
