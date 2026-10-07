<?php

namespace App\Application\UserCompany\SetCompany\DTO;

use App\Shared\Time\ClockInterface;

final class UserSetCompanyResponse
{
    public function __construct(
        private string $userUuid,
        private int $companyId,
        private ClockInterface $clock,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => true,
            'timestamp' => $this->clock->nowFormatted(),
            'message' => 'Компания успешно связана с пользователем',
            'data' => [
                'uuid' => $this->userUuid,
                'company' => $this->companyId,
            ],
        ];
    }
}
