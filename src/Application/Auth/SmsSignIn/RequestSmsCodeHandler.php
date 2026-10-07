<?php

declare(strict_types=1);

namespace App\Application\Auth\SmsSignIn;

use App\Application\Auth\SmsSignIn\Command\RequestSmsCodeCommand;
use App\Application\Auth\SmsSignIn\DTO\RequestSmsCodeResponseDto;
use App\Application\Auth\UserToken\DTO\RequestContext;
use App\Domain\Event\SmsVerificationEventFactory;
use App\Infrastructure\Auth\SmsSignIn\LoginChallengeRedisStorage;
use App\Infrastructure\EventPublisher\HttpSmsEventPublisher;
use App\Shared\Time\ClockInterface;


final class RequestSmsCodeHandler
{
    public function __construct(
        private RequestSmsCodeService $service,
        private LoginChallengeRedisStorage $challengeStorage,
        private SmsVerificationEventFactory $eventFactory,
        private HttpSmsEventPublisher       $smsPublisher,
        private ClockInterface $clock,
    ) {}

    public function handle(RequestSmsCodeCommand $command,
                           RequestContext $context
                          ): RequestSmsCodeResponseDto
    {
        /**
         * 1. business logic
         */
        $result = $this->service->createChallenge($command);

        /**
         * 2. store challenge in Redis
         */
        $this->challengeStorage->save(
            challengeId: $result->challengeId,
            userId: $result->userId,
            phone: $result->phone,
            codeHash: $result->codeHash,
            expiresIn: 300,
            attempts: 0,
            ipAddress: $command->ipAddress,
            userAgent: $command->userAgent,
        );

        // 6. event (SMS sending)
        $event = $this->eventFactory->create(
            $result->challengeId,
            $command->phone,
            $result->code
        );

        $this->smsPublisher->publish($event);
        /**
         * 4. response
         */
        return new RequestSmsCodeResponseDto(
            challengeId: $result->challengeId,
            expiresIn: 300,
            serverTime: $this->clock,
        );
    }
}
