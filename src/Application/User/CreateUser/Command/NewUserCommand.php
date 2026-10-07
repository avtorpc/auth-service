<?php

namespace App\Application\User\CreateUser\Command;

final class NewUserCommand
{
    public function __construct(
        public readonly string $userUuid,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $patronymic,
        public readonly string $phoneNumber,
        public readonly string $verificationChannelId,

        public readonly string $traceId,
        public readonly string $correlationId,
        public readonly string $producerService,
        public readonly string $callback,

        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,

        public readonly array $payload = [],
    ) {}
}
