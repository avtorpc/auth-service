<?php

namespace App\Application\GetFirstToken\Mapper;


use App\Application\GetFirstToken\Command\GetFirstTokenCommand;
use App\Application\GetFirstToken\DTO\GetFirstTokenDto;

final class GetFirstTokenCommandMapper
{
    public static function mapRequestToCommand(
        GetFirstTokenDto $request,
        ?string $ipAddress = null
    ): GetFirstTokenCommand {
        return new GetFirstTokenCommand(
            userUuid: $request->userUuid,
            ipAddress: $ipAddress
        );
    }
}
