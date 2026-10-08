<?php

namespace App\Application\Auth\UserToken;

use App\Application\Auth\UserToken\Command\LoginCommand;
use App\Domain\Dictionaries\DictionaryDomainService;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class AuthValidator
{
    public function __construct(
        private DictionaryDomainService $dictionaryDomainService
    ) {}

    public function validate(LoginCommand $command): void
    {
        if ($command->login === '') {
            throw new BadRequestException(
                'Login cannot be empty',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        if ($command->password === '') {
            throw new BadRequestException(
                'Password cannot be empty',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        $this->validateLoginFormat($command->login);
        $this->validatePasswordFormat($command->password);
    }

    private function validateLoginFormat(string $login): void
    {
        if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException(
                'Invalid email format',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }
    }

    private function validatePasswordFormat(string $password): void
    {
        if (mb_strlen($password) < 6) {
            throw new BadRequestException(
                'Password must be at least 6 characters',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }

        if (strlen($password) > 72) {
            throw new BadRequestException(
                'Password is too long',
                ErrorCode::AUTH_BAD_REQUEST
            );
        }
    }
}
