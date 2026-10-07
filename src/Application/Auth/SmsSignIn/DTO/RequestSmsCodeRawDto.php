<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\DTO;

final class RequestSmsCodeRawDto
{
    public function __construct(
        public mixed $phone,
        public mixed $userAgent,
    ) {
    }
}
