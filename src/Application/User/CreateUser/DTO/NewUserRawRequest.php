<?php

namespace App\Application\User\CreateUser\DTO;

final class NewUserRawRequest
{
    public mixed $eventId = null;

    public mixed $eventType = null;

    public mixed $eventVersion = null;

    public mixed $occurredAt = null;

    public mixed $traceId = null;

    public mixed $correlationId = null;

    public mixed $producerService = null;

    public mixed $callback = null;

    public mixed $requestId = null;

    /**
     * Kafka payload (сырой)
     */
    public array $payload = [];

    /**
     * Дублируем поля из payload (удобство для DomainMapper)
     */
    public mixed $userUuid = null;

    public mixed $email = null;

    public mixed $lastName = null;

    public mixed $firstName = null;

    public mixed $patronymic = null;

    public mixed $phoneNumber = null;

    public mixed $verificationChannelId = null;
}
