<?php
declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
use Doctrine\DBAL\{DriverManager,Tools\DsnParser};
use App\Infrastructure\Auth\DbRefreshTokenRepository;
use App\Infrastructure\DB\SchemaSqlHelper;
use App\Infrastructure\User\UserRepository;
use App\Shared\Time\SystemClock;
use App\Application\Auth\UserToken\AuthAccessTokenService;
use App\Application\Auth\RefreshToken\Command\RefreshTokenCommand;
use App\Shared\Exception\BadRequestException;
$email=$argv[1]??'';
if(!preg_match('/^chat-candidate-[a-f0-9-]+@example\.invalid$/D',$email))throw new RuntimeException('Synthetic account required');
$db=DriverManager::getConnection((new DsnParser(['postgresql'=>'pdo_pgsql']))->parse(getenv('DATABASE_URL')));
$clock=new SystemClock('UTC');$schema=new SchemaSqlHelper(getenv('DB_SCHEMA')?:'auth');$repo=new DbRefreshTokenRepository($db,$schema,$clock);$users=new UserRepository($db,$schema,$clock);$user=$users->findByEmail($email);
if(!$user)throw new RuntimeException('Synthetic account missing');
function check(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
$issue=new App\Application\Auth\UserToken\RefreshTokenService($repo,$clock);
$access=new AuthAccessTokenService(file_get_contents(getenv('JWT_PRIVATE_KEY_FILE')),300,'auth-service','api',$clock);
$service=new App\Application\Auth\RefreshToken\RefreshTokenService($repo,$users,$access,$clock);
$ids=[];
try{
 $one=$issue->create($user,null,null);$row=$repo->findByHash(hash('sha256',$one->raw));$ids[]=$row['id'];check($row['expires_at']===null,'New login expires');
 $two=$issue->create($user,null,null);$ids[]=$repo->findByHash(hash('sha256',$two->raw))['id'];check($repo->findByHash(hash('sha256',$one->raw))!==null,'Another login revoked first');
 $pair=$service->refresh(new RefreshTokenCommand($one->raw,null),null);check($pair->refreshToken!==$one->raw,'Refresh did not rotate');
 check($repo->findByHash(hash('sha256',$pair->refreshToken))['expires_at']===null,'Rotated login expires');
 try{$service->refresh(new RefreshTokenCommand($one->raw,null),null);throw new RuntimeException('Replay accepted');}catch(BadRequestException){}
 $repo->revoke($pair->refreshToken);$repo->revoke($pair->refreshToken);
 try{$service->refresh(new RefreshTokenCommand($pair->refreshToken,null),null);throw new RuntimeException('Revoked login refreshed');}catch(BadRequestException){}
 check($repo->findByHash(hash('sha256',$two->raw))['revoked_at']===null,'Logout revoked another login');
 $db->executeStatement("UPDATE auth.refresh_tokens SET expires_at='2000-01-01' WHERE id=?",[$ids[1]]);
 try{$service->refresh(new RefreshTokenCommand($two->raw,null),null);throw new RuntimeException('Legacy expired login revived');}catch(BadRequestException){}
 echo "PASS: non-expiring refresh, independent logins, rotation, old credential rejection, per-session revocation, expired legacy credential rejection\n";
}finally{foreach($ids as $id)$db->executeStatement('DELETE FROM auth.refresh_tokens WHERE id=? AND user_id=?',[$id,$user->getId()]);}
