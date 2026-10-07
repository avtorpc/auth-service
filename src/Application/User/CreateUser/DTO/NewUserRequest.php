<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser\DTO;

use DateTimeImmutable;

final class NewUserRequest
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $eventType,
        public readonly int $eventVersion,
        public readonly DateTimeImmutable $occurredAt,

        public readonly string $traceId,
        public readonly string $correlationId,

        public readonly string $producerService,
        public readonly string $callback,

        public readonly array $payload,

        // flattened payload (для удобства бизнеса)
        public readonly string $userUuid,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $patronymic,
        public readonly string $phoneNumber,
        public readonly string $verificationChannelId,
    ) {}
}
