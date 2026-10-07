<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn;

use App\Application\Auth\SmsSignIn\Command\RequestSmsCodeCommand;
use App\Application\Auth\SmsSignIn\DTO\RequestSmsCodeResult;
use App\Infrastructure\User\UserRepository;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;
use App\Shared\Utils\VerificationCodeGenerator;
use Ramsey\Uuid\Uuid;

final class RequestSmsCodeService
{
    public function __construct(
        private UserRepository $userRepository,
        private VerificationCodeGenerator $codeGenerator,
    ) {}

    public function createChallenge(RequestSmsCodeCommand $command): RequestSmsCodeResult
    {
        /**
         * 1. Validate user exists
         */
        $user = $this->userRepository->findByPhone($command->phone);

        if (!$user) {
            throw new BadRequestException(
                message: 'User not found',
                errorCode: ErrorCode::AUTH_BAD_REQUEST,
                context: [
                    'phone' => $command->phone,
                ]
            );
        }

        /**
         * 2. Generate challenge ID
         */
        $challengeId = Uuid::uuid4()->toString();;

        /**
         * 3. Generate OTP code
         */
        $code = $this->codeGenerator->generate();

        /**
         * 4. Hash code (never expose raw OTP outside handler boundary)
         */
        $codeHash = hash('sha256', $code);

        /**
         * 5. Return prepared result for handler
         */
        return new RequestSmsCodeResult(
            challengeId: $challengeId,
            userId: $user->getId(),
            phone: $command->phone,
            code: $code,
            codeHash: $codeHash,
        );
    }
}
