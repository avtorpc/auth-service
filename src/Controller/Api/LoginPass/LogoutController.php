<?php
declare(strict_types=1);
namespace App\Controller\Api\LoginPass;
use App\Infrastructure\Auth\DbRefreshTokenRepository;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
final class LogoutController
{
    public function __construct(private DbRefreshTokenRepository $tokens) {}
    #[Route('/logout', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->toArray()['refreshToken'] ?? null;
        if (!is_string($token) || !preg_match('/^[a-f0-9]{128}$/D', $token)) {
            return new JsonResponse(['success'=>false], 400, ['Cache-Control'=>'no-store']);
        }
        // Possession of this opaque credential only revokes its own login; repeat logout is safe.
        $this->tokens->revoke($token);
        return new JsonResponse(['success'=>true], 200, ['Cache-Control'=>'no-store']);
    }
}
