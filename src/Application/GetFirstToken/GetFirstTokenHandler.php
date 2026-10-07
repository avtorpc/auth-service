<?php

namespace App\Application\GetFirstToken;

use App\Application\GetFirstToken\Command\GetFirstTokenCommand;
use App\Application\Auth\UserToken\DTO\AuthTokenResponse;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Domain\Event\UserRegistrationCompletedEmailEventFactory;
use App\Infrastructure\EventPublisher\HttpKafkaEventPublisher;
use App\Shared\Time\ClockInterface;
use Psr\Log\LoggerInterface;

final class GetFirstTokenHandler
{
    public function __construct(
        private GetFirstTokenValidator $validator,
        private FirstUserTokenService $service,
        private LoggerInterface $logger,
        private ClockInterface $clock,
        private UserRegistrationCompletedEmailEventFactory $eventFactory,
        private HttpKafkaEventPublisher      $publisher
    ) {}

    /**
     * Выдача первого access token по user UUID
     */
    public function handle(
        GetFirstTokenCommand $command,
        RequestContext $context
    ): AuthTokenResponse {
        $this->logger->info('First token flow started', [
            'ip' => $context->ip,
            'user_uuid' => $command->userUuid,
        ]);

        // 1. Валидация user identity (существование, статус, блокировки)
        $this->validator->validate($command, $context);

        // 2. Генерация токенов (без refresh rotation)
        $tokenPair = $this->service->issue($command, $context);

        $this->logger->info('First token issued successfully', [
            'ip' => $context->ip,
            'user_uuid' => $command->userUuid,
        ]);



        $user= $this->service->loadUser($command, $context);

        $event = $this->eventFactory->create(
            $user
        );

        $this->publisher->publish($event);

        // 3. Ответ клиенту
        return new AuthTokenResponse(
            accessToken: $tokenPair->accessToken,
            refreshToken: $tokenPair->refreshToken,
            expiresInSeconds: $tokenPair->expiresInSeconds,
            clock: $this->clock
        );
    }
}
