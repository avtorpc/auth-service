<?php

namespace App\Application\Auth\SmsSignIn\DTO;

final class RequestSmsCodeResult
{
    public function __construct(
        public readonly string $challengeId,
        public readonly int $userId,
        public readonly string $phone,
        public readonly string $code,
        public readonly string $codeHash,
    ) {}
}
