<?php

namespace App\Application\Auth\SmsSignIn;

use App\Domain\User\User;
use App\Infrastructure\Auth\SmsSignIn\LoginChallengeRedisStorage;
use App\Infrastructure\User\UserRepository;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class SmsChallengeService
{
    public function __construct(
        private LoginChallengeRedisStorage $storage,
        private UserRepository $userRepository,
    ) {}

    /**
     * Основной flow проверки SMS challenge
     *
     * @return object user (можешь заменить на User DTO/Entity)
     */
    public function validateAndResolve(string $challengeId, string $code): User
    {
        /**
         * 1. Load challenge
         */
        $challenge = $this->storage->get($challengeId);

        if (!$challenge) {
            throw new BadRequestException(
                'Challenge not found or expired',
                ErrorCode::AUTH_BAD_REQUEST,
                ['challengeId' => $challengeId]
            );
        }

        /**
         * 2. Block check
         */
        if ($this->storage->isBlocked($challengeId)) {
            throw new BadRequestException(
                'Too many attempts',
                ErrorCode::AUTH_TOO_MANY_REQUESTS,
                ['challengeId' => $challengeId]
            );
        }

        /**
         * 3. Verify code
         */
        if (!$this->storage->verifyCode($challengeId, $code)) {
            $this->storage->incrementAttempts($challengeId);

            throw new BadRequestException(
                'Invalid verification code',
                ErrorCode::AUTH_BAD_REQUEST,
                ['challengeId' => $challengeId]
            );
        }

        /**
         * 4. Success → cleanup
         */
        $this->storage->clear($challengeId);

        /**
         * 5. Resolve user from challenge
         */
        return $this->resolveUser($challenge);
    }

    /**
     * Здесь связываешь challenge → user
     * (phone/email/whatever)
     */
    private function resolveUser(array $challenge): object
    {

        return $this->userRepository->findById($challenge['user_id']);
    }
}
