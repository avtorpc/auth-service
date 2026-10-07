<?php

namespace App\Application\GetFirstToken;

use App\Application\GetFirstToken\Command\GetFirstTokenCommand;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class GetFirstTokenValidator
{
    public function validate(GetFirstTokenCommand $command): void
    {
        $uuid = $command->userUuid;

        if ($uuid === null || trim($uuid) === '') {
            throw new BadRequestException(
                'User UUID is empty',
                ErrorCode::AUTH_FIELD_EMPTY
            );
        }

        $uuid = trim($uuid);

        if (!$this->isValidUuid($uuid)) {
            throw new BadRequestException(
                'Invalid user UUID format',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }
    }

    private function isValidUuid(string $uuid): bool
    {
        return (bool) preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $uuid
        );
    }
}
