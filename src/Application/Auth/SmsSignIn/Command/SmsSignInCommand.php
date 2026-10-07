<?php

namespace App\Application\Auth\SmsSignIn\Command;

final class SmsSignInCommand
{
    public function __construct(
        public readonly string $challengeId,
        public readonly string $code,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
    ) {}
}
