<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\Mapper;

use App\Application\Auth\SmsSignIn\Command\RequestSmsCodeCommand;
use App\Application\Auth\SmsSignIn\DTO\RequestSmsCodeDto;

final class RequestSmsCodeCommandMapper
{
    public static function mapRequestToCommand(
        RequestSmsCodeDto $request,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): RequestSmsCodeCommand {
        return new RequestSmsCodeCommand(
            phone: $request->phone,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
    }
}
