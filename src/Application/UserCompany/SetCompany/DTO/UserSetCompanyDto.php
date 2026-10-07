<?php

namespace App\Application\UserCompany\SetCompany\DTO;

final class UserSetCompanyDto
{
    public function __construct(
        public readonly string $userUuid,
        public readonly int $companyId,
    ) {}
}
