<?php
declare(strict_types=1);
namespace App\Controller\Api;
use App\Application\Registration\AccountProvisioner;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\{Request,JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
final class RegistrationController {
 public function __construct(private AccountProvisioner $accounts,
  #[Autowire('%env(REGISTRATION_INTERNAL_TOKEN)%')] private string $token) {}
 #[Route('/registration/email-exists',methods:['POST'])]
 #[Route('/registration/complete',methods:['POST'])]
 public function __invoke(Request $request): JsonResponse {
  if(strlen($this->token)<32||!hash_equals($this->token,(string)$request->headers->get('X-Registration-Token')))return new JsonResponse(['success'=>false],403);
  try{
   $p=$request->toArray();
   if(str_ends_with($request->getPathInfo(),'/email-exists')){
    if(!is_string($p['email']??null)||!filter_var($p['email'],FILTER_VALIDATE_EMAIL))return new JsonResponse(['success'=>false],422);
    $result=['success'=>true,'exists'=>$this->accounts->emailExists($p['email'])];
   }else $result=$this->accounts->complete($p);
   return new JsonResponse($result,200,['Cache-Control'=>'no-store']);
  }catch(HttpExceptionInterface $e){return new JsonResponse(['success'=>false],$e->getStatusCode());}
 }
}
