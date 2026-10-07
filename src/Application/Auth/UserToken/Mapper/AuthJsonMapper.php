<?php

namespace App\Application\Auth\UserToken\Mapper;

use App\Application\Auth\UserToken\DTO\AuthRawRequest;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class AuthJsonMapper
{
    public static function fromJson(string $json): AuthRawRequest
    {
        try {
            $data = json_decode(
                $json,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException) {
            throw new BadRequestException(
                'Invalid JSON payload',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        $dto = new AuthRawRequest();

        $dto->login = $data['login'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->userAgent = $data['userAgent'] ?? null;

        return $dto;
    }
}
