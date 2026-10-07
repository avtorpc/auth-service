<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser\Mapper;

use App\Application\User\CreateUser\DTO\NewUserRawRequest;
use JsonException;

final class NewUserJsonMapper
{
    /**
     * JSON string → Raw Request DTO
     */
    public static function fromJson(string $json): NewUserRawRequest
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \InvalidArgumentException(
                'Invalid JSON payload: ' . $e->getMessage()
            );
        }

        return self::map($data);
    }

    /**
     * array → Raw Request DTO
     */
    public static function map(array $data): NewUserRawRequest
    {
        $dto = new NewUserRawRequest();

        $dto->eventId = $data['event_id'] ?? null;
        $dto->eventType = $data['event_type'] ?? null;
        $dto->eventVersion = $data['event_version'] ?? null;
        $dto->occurredAt = $data['occurred_at'] ?? null;

        $dto->traceId = $data['trace_id'] ?? null;
        $dto->correlationId = $data['correlation_id'] ?? null;

        $dto->producerService = $data['producer']['service'] ?? null;
        $dto->callback = $data['callback'] ?? null;

        $dto->payload = $data['payload'] ?? [];

        return $dto;
    }
}
