<?php

namespace App\Application\Auth\UserToken\Mapper;

use App\Application\Auth\UserToken\DTO\AuthRawRequest;
use App\Application\Auth\UserToken\DTO\AuthRequest;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

/**
 * DomainMapper = данные валидны по форме
 */
final class AuthDomainMapper
{
    private const FIELD_LABELS = [
        'login' => 'Логин',
        'password' => 'Пароль',
        'userAgent' => 'User-Agent',
    ];

    public static function map(AuthRawRequest $raw): AuthRequest
    {
        return new AuthRequest(
            login: self::required($raw->login, 'login'),
            password: self::required($raw->password, 'password'),
            userAgent: self::nullableString($raw->userAgent),
        );
    }

    private static function fail(string $field, ErrorCode $code): never
    {
        $label = self::FIELD_LABELS[$field] ?? $field;

        throw new BadRequestException(
            "{$label}: некорректное значение",
            $code
        );
    }

    private static function required(?string $value, string $field): string
    {
        if ($value === null) {
            self::fail($field, ErrorCode::AUTH_FIELD_MISSING);
        }

        $value = trim($value);

        if ($value === '') {
            self::fail($field, ErrorCode::AUTH_FIELD_EMPTY);
        }

        return $value;
    }

    private static function nullableString(?string $value): ?string
    {
        return $value === null ? null : trim($value);
    }
}
