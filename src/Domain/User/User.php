<?php

namespace App\Domain\User;

use App\Application\User\CreateUser\Command\NewUserCommand;
use App\Infrastructure\Security\BcryptPasswordHasher;
use App\Infrastructure\Security\RandomPasswordGenerator;
use App\Shared\Time\SystemClock;

final class User
{
    private static ?BcryptPasswordHasher $hasher = null;
    private static ?RandomPasswordGenerator $passwordGenerator = null;

    public function __construct(
        private int $id,
        private string $uuid,
        private string $email,
        private string $passwordHash,
        private string $firstName,
        private string $lastName,
        private ?string $patronymic = null,
        private ?string $phoneNumber = null,
        private $verificationChannelId,
        private string $createdAt,
        private string $updatedAt,
        private array $roles = ['ROLE_USER'],
        private array $profile = []
    ) {}

    /**
     * Фабрика: создаёт пользователя с автоматически сгенерированным паролем
     */
    public static function createWithRandomPassword(
        NewUserCommand $command,
        int $id =0
    ): self {
        $plainPassword = self::passwordGenerator()->generate();

        return new self(
            id: $id,
            uuid: $command->userUuid,
            email: $command->email,
            passwordHash: self::hasher()->hash($plainPassword),
            firstName: $command->firstName,
            lastName: $command->lastName,
            patronymic: $command->patronymic,
            phoneNumber: $command->phoneNumber,
            verificationChannelId: $command->verificationChannelId,
            createdAt: '',
            updatedAt: '',
            roles: ['ROLE_USER']
        );
    }

    /**
     * Проверка пароля
     */
    public function verifyPassword(string $plainPassword): bool
    {
        return self::hasher()->verify($plainPassword, $this->passwordHash);
    }

    /**
     * Смена пароля
     */
    public function changePassword(string $plainPassword): void
    {
        $this->passwordHash = self::hasher()->hash($plainPassword);
    }

    private static function hasher(): BcryptPasswordHasher
    {
        return self::$hasher ??= new BcryptPasswordHasher();
    }

    private static function passwordGenerator(): RandomPasswordGenerator
    {
        return self::$passwordGenerator ??= new RandomPasswordGenerator();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getProfile(): array
    {
        return $this->profile;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getVerificationChannelId(): string
    {
        return $this->verificationChannelId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
