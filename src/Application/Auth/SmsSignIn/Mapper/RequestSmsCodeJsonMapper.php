<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\Mapper;

use App\Application\Auth\SmsSignIn\DTO\RequestSmsCodeRawDto;

final class RequestSmsCodeJsonMapper
{
    public static function fromJson(string $json): RequestSmsCodeRawDto
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return new RequestSmsCodeRawDto(
                phone: null,
                userAgent: null
            );
        }

        return new RequestSmsCodeRawDto(
            phone: $data['phone'] ?? null,
            userAgent: $data['userAgent'] ?? null,
        );
    }
}
