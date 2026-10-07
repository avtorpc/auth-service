<?php

namespace App\Application\UserCompany\SetCompany\Mapper;

use App\Application\UserCompany\SetCompany\Command\UserSetCompanyCommand;
use App\Application\UserCompany\SetCompany\DTO\UserSetCompanyDto;

final class UserSetCompanyCommandMapper
{
    public static function mapRequestToCommand(
        UserSetCompanyDto $request,
        ?string $ip,
        ?string $userAgent
    ): UserSetCompanyCommand {
        return new UserSetCompanyCommand(
            userUuid: $request->userUuid,
            companyId: $request->companyId,
            ip: $ip,
            userAgent: $userAgent
        );
    }
}
