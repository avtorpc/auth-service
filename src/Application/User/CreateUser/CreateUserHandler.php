<?php

namespace App\Application\User\CreateUser;

use App\Application\User\CreateUser\Command\NewUserCommand;
use App\Domain\User\User;
use App\Infrastructure\User\UserRepository;

final class CreateUserHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * Kafka-style handler:
     * - success => true
     * - failure => exception (handled globally)
     */
    public function handle(NewUserCommand $command): bool
    {
        /**
         * =========================================================
         * 1. CREATE DOMAIN USER
         * =========================================================
         */
        $user = User::createWithRandomPassword(
            $command
        );

        /**
         * =========================================================
         * 2. PERSIST
         * =========================================================
         */
        $userId = $this->userRepository->save($user);

        /**
         * =========================================================
         * 3. VALIDATION OF RESULT
         * =========================================================
         */
        if (!$userId) {
            throw new UserCreationFailedException(
                'User was not persisted'
            );
        }

        /**
         * =========================================================
         * 4. SUCCESS
         * =========================================================
         */
        return true;
    }
}
