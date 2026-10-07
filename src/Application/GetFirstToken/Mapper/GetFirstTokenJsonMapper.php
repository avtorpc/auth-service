<?php

namespace App\Application\GetFirstToken\Mapper;

use App\Application\GetFirstToken\DTO\GetFirstTokenRawDto;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class GetFirstTokenJsonMapper
{
    public static function fromJson(string $json): GetFirstTokenRawDto
    {
        try {
            $data = json_decode(
                $json,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new BadRequestException(
                'Invalid JSON payload',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        return new GetFirstTokenRawDto(
            userUuid: $data['userUUID'] ?? null,
        );
    }
}
