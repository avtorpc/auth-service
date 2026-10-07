<?php

namespace App\Infrastructure\EventPublisher;

use App\Domain\Event\UserRegistrationCompletedEmailEvent;

final class UserRegistrationCompletedEmailEventSerializer
{
    public function toArray(UserRegistrationCompletedEmailEvent $event): array
    {
        return [
            'event_id' => $event->eventId,
            'event_type' => $event->eventType,
            'event_version' => $event->eventVersion,
            'occurred_at' => $event->occurredAt,

            'trace_id' => $event->traceId,
            'correlation_id' => $event->correlationId,

            'producer' => [
                'service' => $event->service,
            ],

            'callback' => '',

            'payload' => [
                'user_uuid' => $event->userUuid,
                'email' => $event->email,
                'first_name' => $event->firstName,
                'last_name' => $event->lastName,
                'patronymic' => $event->patronymic,
                'phone_number' => $event->phoneNumber,
                'verification_channel_id' => $event->verificationChannelId,

                'created_at' => $event->createdAt,
                'updated_at' => $event->updatedAt,
            ],
        ];
    }
}
