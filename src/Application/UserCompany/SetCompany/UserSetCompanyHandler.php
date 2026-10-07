<?php

namespace App\Application\UserCompany\SetCompany;

use App\Application\UserCompany\SetCompany\Command\UserSetCompanyCommand;
use App\Application\UserCompany\SetCompany\DTO\UserSetCompanyResponse;
use App\Infrastructure\User\UserCompanyLinkRepository;
use App\Infrastructure\User\UserRepository;
use App\Shared\Exception\ConflictException;
use App\Shared\Exception\ErrorCode;
use App\Shared\Exception\NotFoundException;
use App\Shared\Time\ClockInterface;
use Psr\Log\LoggerInterface;

final class UserSetCompanyHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserCompanyLinkRepository $linkRepository,
        private ClockInterface $clock,
        private LoggerInterface $logger,
    ) {}

    public function handle(UserSetCompanyCommand $command): UserSetCompanyResponse
    {
        $this->logger->info('User company link started', [
            'user_uuid' => $command->userUuid,
            'company_id' => $command->companyId,
            'ip' => $command->ip,
            'user_agent' => $command->userAgent,
        ]);

        if ($this->userRepository->findByUuid($command->userUuid) === null) {
            throw new NotFoundException(
                'User not found',
                ErrorCode::AUTH_USER_NOT_FOUND,
                ['user_uuid' => $command->userUuid]
            );
        }

        $existingCompanyLink = $this->linkRepository->findByCompanyId($command->companyId);

        if (
            $existingCompanyLink !== null
            && $existingCompanyLink['user_uuid'] !== $command->userUuid
        ) {
            throw new ConflictException(
                'Company is already linked to another user',
                ErrorCode::AUTH_CONFLICT,
                [
                    'user_uuid' => $command->userUuid,
                    'company_id' => $command->companyId,
                    'linked_user_uuid' => $existingCompanyLink['user_uuid'],
                ]
            );
        }

        $this->linkRepository->upsert(
            $command->userUuid,
            $command->companyId
        );

        $this->logger->info('User company link finished', [
            'user_uuid' => $command->userUuid,
            'company_id' => $command->companyId,
        ]);

        return new UserSetCompanyResponse(
            userUuid: $command->userUuid,
            companyId: $command->companyId,
            clock: $this->clock
        );
    }
}
