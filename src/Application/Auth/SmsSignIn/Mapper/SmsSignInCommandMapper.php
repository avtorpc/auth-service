<?php

namespace App\Application\Auth\SmsSignIn\Mapper;

use App\Application\Auth\SmsSignIn\Command\SmsSignInCommand;
use App\Application\Auth\SmsSignIn\DTO\SmsSignInDto;

final class SmsSignInCommandMapper
{
    public static function mapRequestToCommand(
        SmsSignInDto $dto,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): SmsSignInCommand {
        return new SmsSignInCommand(
            challengeId: $dto->challengeId,
            code: $dto->code,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );
    }
}
