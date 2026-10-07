<?php

namespace App\Application\User\CreateUser;

use App\Domain\User\User;
use App\Infrastructure\User\UserRepository;
use App\Infrastructure\Auth\PasswordHasher;
use App\Shared\Time\ClockInterface;
use Doctrine\DBAL\Connection;
use App\Infrastructure\DB\SchemaSqlHelper;

final class CreateUserService
{
    public function __construct(
        private Connection $connection,
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private ClockInterface $clock,
        private SchemaSqlHelper $schemaSqlHelper,
    ) {}

    public function create(NewUserRequest $request): User
    {
        /**
         * 1. защита от дублей
         */
        if ($this->userRepository->findByEmail($request->email)) {
            throw new \DomainException('User already exists');
        }

        /**
         * 2. генерация password (если приходит извне — можно заменить)
         */
        $password = $request->password ?? bin2hex(random_bytes(8));
        $passwordHash = $this->passwordHasher->hash($password);

        $now = $this->clock->now();

        /**
         * 3. insert
         */
        $this->connection->insert(
            $this->schemaSqlHelper->table('users'),
            [
                'user_uuid' => $request->userUuid,
                'email' => $request->email,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'patronymic' => $request->patronymic,
                'phone_number' => $request->phoneNumber,
                'verification_channel_id' => $request->verificationChannelId,
                'password_hash' => $passwordHash,
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]
        );

        /**
         * 4. вернуть domain user (или можно reload)
         */
        return $this->userRepository->findByEmail($request->email);
    }
}
