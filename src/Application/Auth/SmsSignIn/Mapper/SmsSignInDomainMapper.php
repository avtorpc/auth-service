<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\Mapper;

use App\Application\Auth\SmsSignIn\DTO\SmsSignInDto;
use App\Application\Auth\SmsSignIn\DTO\SmsSignInRawDto;
use App\Shared\Exception\BadRequestException;

final class SmsSignInDomainMapper
{
    public static function map(SmsSignInRawDto $raw): SmsSignInDto
    {
        $challengeId = is_string($raw->challengeId ?? null)
            ? trim($raw->challengeId)
            : null;

        $code = is_string($raw->code ?? null)
            ? trim($raw->code)
            : null;

        $userAgent = is_string($raw->userAgent ?? null)
            ? trim($raw->userAgent)
            : 'unknown';

        $ip = is_string($raw->ip ?? null)
            ? trim($raw->ip)
            : null;

        if (!$challengeId) {
            throw new BadRequestException('challengeId is required');
        }

        if (!$code) {
            throw new BadRequestException('code is required');
        }

        return new SmsSignInDto(
            challengeId: $challengeId,
            code: $code
        );
    }
}
