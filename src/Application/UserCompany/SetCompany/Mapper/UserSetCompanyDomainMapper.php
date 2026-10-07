<?php

namespace App\Application\UserCompany\SetCompany\Mapper;

use App\Application\UserCompany\SetCompany\DTO\UserSetCompanyDto;
use App\Application\UserCompany\SetCompany\DTO\UserSetCompanyRawDto;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class UserSetCompanyDomainMapper
{
    public static function map(UserSetCompanyRawDto $raw): UserSetCompanyDto
    {
        return new UserSetCompanyDto(
            userUuid: self::uuid($raw->uuid),
            companyId: self::companyId($raw->company),
        );
    }

    private static function uuid(mixed $value): string
    {
        if ($value === null || $value === '') {
            throw new BadRequestException(
                'uuid is required',
                ErrorCode::AUTH_FIELD_REQUIRED,
                ['field' => 'uuid']
            );
        }

        if (!is_string($value)) {
            throw new BadRequestException(
                'uuid must be string',
                ErrorCode::AUTH_BAD_REQUEST,
                ['field' => 'uuid']
            );
        }

        $value = trim($value);

        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $value)) {
            throw new BadRequestException(
                'uuid is invalid',
                ErrorCode::AUTH_BAD_REQUEST,
                ['field' => 'uuid']
            );
        }

        return strtolower($value);
    }

    private static function companyId(mixed $value): int
    {
        if ($value === null || $value === '') {
            throw new BadRequestException(
                'company is required',
                ErrorCode::AUTH_FIELD_REQUIRED,
                ['field' => 'company']
            );
        }

        if (is_string($value) && ctype_digit($value)) {
            $value = (int) $value;
        }

        if (!is_int($value) || $value <= 0) {
            throw new BadRequestException(
                'company is invalid',
                ErrorCode::AUTH_BAD_REQUEST,
                ['field' => 'company']
            );
        }

        return $value;
    }
}
