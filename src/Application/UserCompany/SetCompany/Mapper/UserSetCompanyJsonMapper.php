<?php

namespace App\Application\UserCompany\SetCompany\Mapper;

use App\Application\UserCompany\SetCompany\DTO\UserSetCompanyRawDto;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class UserSetCompanyJsonMapper
{
    public static function fromJson(string $json): UserSetCompanyRawDto
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new BadRequestException(
                'Invalid JSON',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        $dto = new UserSetCompanyRawDto();
        $dto->uuid = $data['uuid'] ?? null;
        $dto->company = $data['company'] ?? null;

        return $dto;
    }
}
