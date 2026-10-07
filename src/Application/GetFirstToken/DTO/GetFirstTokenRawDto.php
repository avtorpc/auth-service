<?php

namespace App\Application\GetFirstToken\DTO;

final class GetFirstTokenRawDto
{
    public function __construct(
        public ?string $userUuid,
    ) {}
}
