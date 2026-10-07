<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\SmsSignIn;

use Predis\Client;
use App\Shared\Exception\BadRequestException;
use App\Shared\Exception\ErrorCode;

final class LoginChallengeRedisStorage
{
    private const PREFIX = 'auth_sms_challenge_';

    public function __construct(
        private Client $redis
    ) {}

    public function save(
        string $challengeId,
        int $userId,
        string $phone,
        string $codeHash,
        int $expiresIn,
        int $attempts = 0,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): void {
        $key = $this->key($challengeId);

        $this->redis->del($key);

        $this->redis->hMSet($key, [
            'challenge_id' => $challengeId,
            'user_id' => $userId,
            'phone' => $phone,
            'code_hash' => $codeHash,
            'attempts' => $attempts,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);

        $this->redis->expire($key, $expiresIn);
    }

    public function get(string $challengeId): ?array
    {
        $data = $this->redis->hGetAll($this->key($challengeId));

        if (!$data || empty($data['challenge_id'])) {
            return null;
        }

        return [
            'challenge_id' => $data['challenge_id'],
            'user_id' => (int) $data['user_id'],
            'phone' => $data['phone'],
            'code_hash' => $data['code_hash'],
            'attempts' => (int) $data['attempts'],
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'created_at' => $data['created_at'] ?? null,
        ];
    }

    /**
     * Увеличение попыток
     */
    public function incrementAttempts(string $challengeId): int
    {
        $key = $this->key($challengeId);

        if (!$this->redis->exists($key)) {
            throw new BadRequestException(
                message: 'Challenge expired or not found',
                errorCode: ErrorCode::AUTH_BAD_REQUEST,
                context: ['challengeId' => $challengeId]
            );
        }

        return (int) $this->redis->hIncrBy($key, 'attempts', 1);
    }

    /**
     * Проверка кода (без сайд-эффектов)
     */
    public function verifyCode(string $challengeId, string $code): bool
    {
        $challenge = $this->get($challengeId);

        if (!$challenge) {
            return false;
        }

        return hash_equals(
            $challenge['code_hash'],
            hash('sha256', $code)
        );
    }

    /**
     * Количество попыток
     */
    public function getAttempts(string $challengeId): int
    {
        $challenge = $this->get($challengeId);

        return $challenge['attempts'] ?? 0;
    }

    /**
     * Проверка блокировки (добавляем логику на уровне storage)
     */
    public function isBlocked(string $challengeId, int $maxAttempts = 5): bool
    {
        $attempts = $this->getAttempts($challengeId);

        return $attempts >= $maxAttempts;
    }

    /**
     * Удаление challenge (semantic alias)
     */
    public function clear(string $challengeId): void
    {
        $this->redis->del($this->key($challengeId));
    }

    public function delete(string $challengeId): void
    {
        $this->clear($challengeId);
    }

    public function getUserId(string $challengeId): ?int
    {
        $challenge = $this->get($challengeId);

        return $challenge['user_id'] ?? null;
    }

    public function isExpired(string $challengeId): bool
    {
        return !$this->redis->exists($this->key($challengeId));
    }

    private function key(string $challengeId): string
    {
        return self::PREFIX . $challengeId;
    }
}
