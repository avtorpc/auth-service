<?php

namespace App\Controller\User;

use App\Application\UserCabinet\Mapper\UserCabinetResponseMapper;
use App\Infrastructure\Security\JwtAuthenticator;
use App\Infrastructure\User\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserInfoCabinetController
{
    public function __construct(
        private JwtAuthenticator $jwtAuthenticator,
        private UserRepository $userRepository,
        private UserCabinetResponseMapper $responseMapper
    ) {}

    #[Route('/user/me', name:"UserInfoCabinet", methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $auth = $this->jwtAuthenticator->authenticate($request);

        $user = $this->userRepository->findByUuid($auth->sub);

        if ($user === null) {
            return new JsonResponse(['success' => false, 'error' => 'USER_NOT_FOUND'], 404);
        }

        $response = $this->responseMapper->map($user);

        return new JsonResponse($response->toArray());
    }
}
