<?php

namespace App\Application\UserCabinet\Mapper;

use App\Application\UserCabinet\DTO\UserCabinetResponse;
use App\Domain\User\User;
use App\Shared\Time\ClockInterface;

final class UserCabinetResponseMapper
{
    public function __construct(
        private ClockInterface $clock,
    ) {
    }

    public function map(User $user, ?int $companyId): UserCabinetResponse
    {
        return new UserCabinetResponse(
            uuid: $user->getUuid(),
            email: $user->getEmail(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            patronymic: $user->getPatronymic(),
            phoneNumber: $user->getPhoneNumber(),
            verificationChannelId: $user->getVerificationChannelId(),
            roles: $user->getRoles(),
            companyId: $companyId,
            createdAt: $user->getCreatedAt(),
            updatedAt: $user->getUpdatedAt(),
            clock: $this->clock,
        );
    }
}
