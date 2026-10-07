<?php

namespace App\Application\GetFirstToken\Command;

final class GetFirstTokenCommand
{
    public function __construct(
        public string $userUuid,
        public ?string $ipAddress,
    ) {}
}
