<?php

namespace App\Application\UserCabinet\DTO;

use App\Shared\Time\ClockInterface;

final class UserCabinetResponse
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private string $uuid,
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $patronymic,
        private string $phoneNumber,
        private string $verificationChannelId,
        private array $roles,
        private ?int $companyId,
        private string $createdAt,
        private string $updatedAt,
        private ClockInterface $clock,
    ) {
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'timestamp' => $this->clock->nowFormatted(),
            'data' => [
                'uuid' => $this->uuid,
                'email' => $this->email,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'patronymic' => $this->patronymic,
                'phoneNumber' => $this->phoneNumber,
                'verificationChannelId' => $this->verificationChannelId,
                'roles' => $this->roles,
                'company' => $this->companyId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ],
        ];
    }
}
