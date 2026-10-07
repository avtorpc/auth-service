<?php

namespace App\Application\Auth\SmsSignIn\DTO;

final class SmsSignInRawDto
{
    public function __construct(
        public readonly ?string $challengeId,
        public readonly ?string $code
    ) {}
}
