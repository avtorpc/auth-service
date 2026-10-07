<?php

namespace App\Application\UserCompany\SetCompany\Command;

final class UserSetCompanyCommand
{
    public function __construct(
        public readonly string $userUuid,
        public readonly int $companyId,
        public readonly ?string $ip,
        public readonly ?string $userAgent,
    ) {}
}
