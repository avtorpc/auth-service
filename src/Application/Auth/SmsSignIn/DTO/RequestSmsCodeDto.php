<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\DTO;

final class RequestSmsCodeDto
{
    public function __construct(
        public string $phone,
        public string $userAgent,
    ) {
    }
}
