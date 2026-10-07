<?php

namespace App\Application\Auth\RefreshToken\Mapper;

use App\Application\Auth\RefreshToken\DTO\RefreshTokenDto;
use App\Application\Auth\RefreshToken\DTO\RefreshTokenRawDto;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

/**
 * DomainMapper = данные валидны по форме
 */
final class RefreshDomainMapper
{
    private const FIELD_LABELS = [
        'refreshToken' => 'Refresh token',
    ];

    public static function map(
        RefreshTokenRawDto $raw
    ): RefreshTokenDto {
        return new RefreshTokenDto(
            refreshToken: self::required(
                $raw->refreshToken,
                'refreshToken'
            ),
        );
    }

    private static function fail(
        string $field,
        ErrorCode $code
    ): never {
        $label = self::FIELD_LABELS[$field] ?? $field;

        throw new BadRequestException(
            "{$label}: некорректное значение",
            $code
        );
    }

    private static function required(
        mixed $value,
        string $field
    ): string {
        if ($value === null) {
            self::fail(
                $field,
                ErrorCode::AUTH_FIELD_MISSING
            );
        }

        if (!is_string($value)) {
            self::fail(
                $field,
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        $value = trim($value);

        if ($value === '') {
            self::fail(
                $field,
                ErrorCode::AUTH_FIELD_EMPTY
            );
        }

        return $value;
    }
}
