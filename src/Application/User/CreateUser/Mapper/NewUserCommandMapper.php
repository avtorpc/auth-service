<?php

namespace App\Application\User\CreateUser\Mapper;

use App\Application\User\CreateUser\DTO\NewUserRequest;
use App\Application\User\CreateUser\Command\NewUserCommand;

final class NewUserCommandMapper
{
    public static function map(
        NewUserRequest $request,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): NewUserCommand {
        return new NewUserCommand(
            userUuid: $request->userUuid,
            email: $request->email,
            firstName: $request->firstName,
            lastName: $request->lastName,
            patronymic: $request->patronymic,
            phoneNumber: $request->phoneNumber,
            verificationChannelId: $request->verificationChannelId,

            traceId: $request->traceId,
            correlationId: $request->correlationId,

            producerService: $request->producerService,
            callback: $request->callback,

            ipAddress: $ipAddress,
            userAgent: $userAgent,

            payload: $request->payload,
        );
    }
}
