<?php

declare(strict_types=1);

namespace App\Application\Auth\UserToken;

use App\Application\Auth\UserToken\Command\LoginCommand;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Domain\Settings\AppSettingsService;
use App\Infrastructure\RateLimit\EmailRateLimiter;
use App\Shared\Exception\ErrorCode;
use App\Shared\Exception\TooManyRequestsException;

final class AuthAttemptValidator
{
    public function __construct(
        private EmailRateLimiter $emailLimiter,
        private AppSettingsService $appSettingsService,
    ) {}

    /**
     * Универсальная защита auth endpoint (login/register/etc)
     */
    public function assertAuthAllowed(
        LoginCommand $command,
        RequestContext $context
    ): void {
        $this->assertEmailRateLimit($command->login, $context);
    }

    /**
     * Email rate limit (brute force protection)
     */
    private function assertEmailRateLimit(
        string $email,
        RequestContext $context
    ): void {
        $cooldown = $this->appSettingsService->getEmailCooldown();

        $acquired = $this->emailLimiter->tryAcquire($email, $cooldown);

        if ($acquired) {
            return;
        }

        throw new TooManyRequestsException(
            'Слишком частые попытки входа.',
            ErrorCode::AUTH_TOO_MANY_REQUESTS,
            [
                'type' => 'email_rate_limit_exceeded',
                'email' => $email,
                'cooldown_seconds' => $cooldown,
                'request' => [
                    'ip' => $context->ip,
                    'uri' => $context->uri,
                    'method' => $context->method,
                    'user_agent' => $context->userAgent,
                ],
            ]
        );
    }
}
