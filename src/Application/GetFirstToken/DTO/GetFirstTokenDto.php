<?php

namespace App\Application\GetFirstToken\DTO;

final class GetFirstTokenDto
{
    public function __construct(
        public string $userUuid,
    ) {}
}
