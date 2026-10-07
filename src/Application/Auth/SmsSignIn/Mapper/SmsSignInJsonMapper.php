<?php

namespace App\Application\Auth\SmsSignIn\Mapper;

use App\Application\Auth\SmsSignIn\DTO\SmsSignInRawDto;

final class SmsSignInJsonMapper
{
    public static function fromJson(string $json): SmsSignInRawDto
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new SmsSignInRawDto(
            challengeId: $data['challengeId'] ?? null,
            code: $data['code'] ?? null,
        );
    }
}
