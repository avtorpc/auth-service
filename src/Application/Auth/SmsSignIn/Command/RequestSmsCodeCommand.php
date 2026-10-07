<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\Command;

final class RequestSmsCodeCommand
{
    public function __construct(
        public string $phone,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {
    }
}
