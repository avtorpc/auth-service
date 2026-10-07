<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn\DTO;

use App\Shared\Time\ClockInterface;

final class RequestSmsCodeResponseDto
{
    public function __construct(
        public readonly string             $challengeId,
        public readonly int                $expiresIn,
        private ClockInterface             $serverTime
    ){}

    public function toArray(): array
    {
        return [
            'success' => true,
            'timestamp' => $this->serverTime->nowFormatted(),
                'data' => [
                'challengeId' => $this->challengeId,
                'expiresIn' => $this->expiresIn,
                'serverTime' => $this->serverTime->nowFormatted()
            ]
        ];
    }
}
