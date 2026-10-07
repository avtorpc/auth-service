<?php

namespace App\Application\GetFirstToken\Mapper;

use App\Application\GetFirstToken\DTO\GetFirstTokenDto;
use App\Application\GetFirstToken\DTO\GetFirstTokenRawDto;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

/**
 * DomainMapper = данные валидны по форме
 */
final class GetFirstTokenDomainMapper
{
    private const FIELD_LABELS = [
        'userUuid' => 'User UUID',
    ];

    public static function map(
        GetFirstTokenRawDto $raw
    ): GetFirstTokenDto {
        return new GetFirstTokenDto(
            userUuid: self::required(
                $raw->userUuid,
                'userUuid'
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
