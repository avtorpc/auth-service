<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser\Mapper;

use App\Application\User\CreateUser\DTO\NewUserRawRequest;
use App\Application\User\CreateUser\DTO\NewUserRequest;
use DateTimeImmutable;
use InvalidArgumentException;

final class NewUserDomainMapper
{
    public static function map(NewUserRawRequest $raw): NewUserRequest
    {
        self::assertRequired($raw);

        return new NewUserRequest(
            eventId: (string) $raw->eventId,
            eventType: (string) $raw->eventType,
            eventVersion: (int) $raw->eventVersion,
            occurredAt: new DateTimeImmutable((string) $raw->occurredAt),

            traceId: (string) $raw->traceId,
            correlationId: (string) $raw->correlationId,

            producerService: (string) $raw->producerService,
            callback: (string) $raw->callback,

            payload: (array) $raw->payload,

            userUuid: self::getPayload($raw, 'user_uuid'),
            email: self::normalizeEmail(self::getPayload($raw, 'email')),
            firstName: (string) self::getPayload($raw, 'first_name'),
            lastName: (string) self::getPayload($raw, 'last_name'),
            patronymic: self::getPayloadNullable($raw, 'patronymic'),

            phoneNumber: (string) self::getPayload($raw, 'phone_number'),
            verificationChannelId: (string) self::getPayload($raw, 'verification_channel_id'),
        );
    }

    private static function assertRequired(NewUserRawRequest $raw): void
    {
        $required = [
            'eventId',
            'eventType',
            'eventVersion',
            'occurredAt',
            'traceId',
            'correlationId',
            'producerService',
            'payload',
        ];

        foreach ($required as $field) {
            if (empty($raw->{$field})) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }

    private static function getPayload(NewUserRawRequest $raw, string $key): mixed
    {
        if (!array_key_exists($key, $raw->payload)) {
            throw new InvalidArgumentException("Missing payload field: {$key}");
        }

        return $raw->payload[$key];
    }

    private static function getPayloadNullable(NewUserRawRequest $raw, string $key): mixed
    {
        return $raw->payload[$key] ?? null;
    }

    private static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}
