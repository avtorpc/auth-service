<?php

namespace App\Domain\Event;

use App\Domain\User\User;
use App\Shared\Time\ClockInterface;
use Ramsey\Uuid\Uuid;

final class UserRegistrationCompletedEmailEventFactory
{

    public function __construct(
        private readonly ClockInterface $clock
    ) {}

    public function create(User $user): UserRegistrationCompletedEmailEvent
    {
        return new UserRegistrationCompletedEmailEvent(
            eventId: Uuid::uuid4()->toString(),
            eventType: 'email.send.registration_completed',
            eventVersion: 1,

            occurredAt: $this->clock->nowIso(),

            traceId: $user->getUuid(),
            correlationId: $user->getUuid(),

            service: 'auth-service',

            userUuid: $user->getUuid(),
            email: $user->getEmail(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            patronymic: $user->getPatronymic(),
            phoneNumber: $user->getPhoneNumber(),
            verificationChannelId: $user->getVerificationChannelId(),

            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt(),
        );
    }
}
