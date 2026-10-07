<?php

namespace App\Application\Auth\UserToken;

use App\Application\Auth\UserToken\DTO\RefreshTokenResult;
use App\Domain\User\User;
use App\Infrastructure\Auth\DbRefreshTokenRepository;
use App\Shared\Time\ClockInterface;
use DateInterval;

final class RefreshTokenService
{
    public function __construct(
        private DbRefreshTokenRepository $repository,
        private ClockInterface $clock,
        private int $ttlDays = 30,
    ) {
    }

    public function create(
        User $user,
        ?string $userAgent,
        ?string $ipAddress,
    ): RefreshTokenResult {
        $rawToken = bin2hex(random_bytes(64));

        $tokenHash = hash('sha256', $rawToken);

        $expiresAt = $this->clock
            ->now()
            ->add(new DateInterval(sprintf('P%dD', $this->ttlDays)));

        $this->repository->save(
            userId: $user->getId(),
            tokenHash: $tokenHash,
            expiresAt: $expiresAt,
//            userAgent: $userAgent,
//            ipAddress: $ipAddress,
        );

        return new RefreshTokenResult(
            raw: $rawToken,
            expiresAt: $expiresAt
        );
    }
}
