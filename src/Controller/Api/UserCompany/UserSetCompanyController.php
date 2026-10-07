<?php

namespace App\Controller\Api\UserCompany;

use App\Application\UserCompany\SetCompany\Mapper\UserSetCompanyCommandMapper;
use App\Application\UserCompany\SetCompany\Mapper\UserSetCompanyDomainMapper;
use App\Application\UserCompany\SetCompany\Mapper\UserSetCompanyJsonMapper;
use App\Application\UserCompany\SetCompany\UserSetCompanyHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class UserSetCompanyController
{
    public function __construct(
        private UserSetCompanyHandler $handler,
    ) {}

    #[Route('/user-set-company', name: 'AuthUserSetCompany', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $raw = UserSetCompanyJsonMapper::fromJson($request->getContent());
        $dto = UserSetCompanyDomainMapper::map($raw);

        $command = UserSetCompanyCommandMapper::mapRequestToCommand(
            $dto,
            $request->getClientIp(),
            $request->headers->get('User-Agent')
        );

        $response = $this->handler->handle($command);

        return new JsonResponse($response->toArray());
    }
}
