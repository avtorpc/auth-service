<?php

namespace App\Application\Auth\UserToken\DTO;

final class RequestContext
{
    public function __construct(
        public ?string $ip,
        public ?string $uri,
        public ?string $method,
        public ?string $userAgent,
    ) {}
}
