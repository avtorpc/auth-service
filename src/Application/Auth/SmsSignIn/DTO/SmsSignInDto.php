<?php

namespace App\Application\Auth\SmsSignIn\DTO;

use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class SmsSignInDto
{
    public function __construct(
        public readonly string $challengeId,
        public readonly string $code,
    ) {}

    public static function fromRaw(SmsSignInRawDto $raw): self
    {
        $challengeId = is_string($raw->challengeId) ? trim($raw->challengeId) : '';
        $code = is_string($raw->code) ? trim($raw->code) : '';

        if ($challengeId === '') {
            throw new BadRequestException(
                'challengeId is required',
                ErrorCode::AUTH_BAD_REQUEST,
                ['field' => 'challengeId']
            );
        }

        if ($code === '') {
            throw new BadRequestException(
                'code is required',
                ErrorCode::AUTH_BAD_REQUEST,
                ['field' => 'code']
            );
        }

        return new self(
            challengeId: $challengeId,
            code: $code
        );
    }
}
