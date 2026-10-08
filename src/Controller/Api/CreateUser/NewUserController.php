<?php

namespace App\Controller\Api\CreateUser;

use App\Application\User\CreateUser\CreateUserHandler;
use App\Application\User\CreateUser\Mapper\NewUserCommandMapper;
use App\Application\User\CreateUser\Mapper\NewUserDomainMapper;
use App\Application\User\CreateUser\Mapper\NewUserJsonMapper;
use App\Shared\Time\SystemClock;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Внутренний контроллер для создания пользователя после регистрации
 */
final class NewUserController
{
    public function __construct(
        private CreateUserHandler $handler
    ) {}
    public function __invoke(Request $request): JsonResponse
    {
        $raw = NewUserJsonMapper::fromJson($request->getContent());
        $dto = NewUserDomainMapper::map($raw);

        $command = NewUserCommandMapper::map(
            $dto,
            $request->getClientIp(),
            $request->headers->get('User-Agent')
        );

        $success = $this->handler->handle($command);

        return new JsonResponse([
            'success' => $success,
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ], $success ? 201 : 400);
    }
}
