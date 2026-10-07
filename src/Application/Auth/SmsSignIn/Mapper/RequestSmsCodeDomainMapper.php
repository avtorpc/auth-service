<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\Mapper;

use App\Application\Auth\SmsSignIn\DTO\RequestSmsCodeDto;
use App\Application\Auth\SmsSignIn\DTO\RequestSmsCodeRawDto;
use App\Shared\Exception\BadRequestException;

final class RequestSmsCodeDomainMapper
{
    public static function map(RequestSmsCodeRawDto $raw): RequestSmsCodeDto
    {
        $phone = is_string($raw->phone) ? trim($raw->phone) : null;
        $userAgent = is_string($raw->userAgent) ? trim($raw->userAgent) : null;

        if (!$phone) {
            throw new BadRequestException('Phone is required');
        }

        if (!$userAgent) {
            $userAgent = 'unknown';
        }

        return new RequestSmsCodeDto(
            phone: $phone,
            userAgent: $userAgent
        );
    }
}
