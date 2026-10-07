<?php

namespace App\Controller\User;

use App\Application\UserCabinet\Mapper\UserCabinetResponseMapper;
use App\Infrastructure\Security\JwtAuthenticator;
use App\Infrastructure\User\UserCompanyLinkRepository;
use App\Infrastructure\User\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserInfoCabinetController
{
    public function __construct(
      //  private CreateUserHandler $handler,
        private JwtAuthenticator $jwtAuthenticator,
        private UserRepository $userRepository,
        private UserCompanyLinkRepository $userCompanyLinkRepository,
        private UserCabinetResponseMapper $responseMapper
    ) {}

    #[Route('/user/me', name:"UserInfoCabinet", methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $auth = $this->jwtAuthenticator->authenticate($request);

        $user = $this->userRepository->findByUuid($auth->sub);
        $companyId = $this->userCompanyLinkRepository->findCompanyIdByUserUuid($auth->sub);

        $response = $this->responseMapper->map($user, $companyId);

        return new JsonResponse($response->toArray());
    }
}
