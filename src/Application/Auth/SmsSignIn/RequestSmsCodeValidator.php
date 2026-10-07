<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn;

use App\Application\Auth\SmsSignIn\Command\RequestSmsCodeCommand;
use App\Application\Auth\User\UserRepository;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class RequestSmsCodeValidator
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function validate(
        RequestSmsCodeCommand $command,
        RequestContext $context
    ): void {
        $this->validatePhoneNotEmpty($command, $context);
        $this->validatePhoneFormat($command, $context);
        $this->validateUserExists($command, $context);
    }

    private function validatePhoneNotEmpty(
        RequestSmsCodeCommand $command,
        RequestContext $context
    ): void {
        if ($command->phone === '') {
            throw new BadRequestException(
                message: 'Phone cannot be empty',
                errorCode: ErrorCode::AUTH_BAD_REQUEST,
                context: [
                    'field' => 'phone',
                    'value' => null,
                    'ip' => $context->ip,
                    'userAgent' => $context->userAgent,
                ]
            );
        }
    }

    private function validatePhoneFormat(
        RequestSmsCodeCommand $command,
        RequestContext $context
    ): void {
        if (!preg_match('/^\+?[0-9]{10,15}$/', $command->phone)) {
            throw new BadRequestException(
                message: 'Invalid phone format',
                errorCode: ErrorCode::AUTH_BAD_REQUEST,
                context: [
                    'field' => 'phone',
                    'value' => $command->phone,
                    'ip' => $context->ip,
                    'userAgent' => $context->userAgent,
                ]
            );
        }
    }

    private function validateUserExists(
        RequestSmsCodeCommand $command,
        RequestContext $context
    ): void {
        $user = $this->userRepository->findByPhone($command->phone);

        if (!$user) {
            throw new BadRequestException(
                message: 'Invalid request',
                errorCode: ErrorCode::AUTH_BAD_REQUEST,
                context: [
                    'phone' => $command->phone,
                    'ip' => $context->ip,
                    'userAgent' => $context->userAgent,
                ]
            );
        }
    }
}
