<?php
declare(strict_types=1);
namespace App\Application\Registration;
use Doctrine\DBAL\Connection;
use App\Infrastructure\DB\SchemaSqlHelper;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException,ConflictHttpException};
final class AccountProvisioner {
 public function __construct(private Connection $db,private SchemaSqlHelper $schema) {}
 public function emailExists(string $email): bool {
  return (bool)$this->db->fetchOne('SELECT 1 FROM '.$this->schema->table('users').' WHERE lower(email)=:email',['email'=>strtolower(trim($email))]);
 }
 public function complete(array $p): array {
  $id=$p['requestId']??null;$email=$p['email']??null;$role=$p['roleCode']??null;$profile=$p['profile']??null;$hash=$p['passwordHash']??null;
  if(!is_string($id)||!Uuid::isValid($id)||!is_string($email)||strlen($email)>255||!filter_var($email,FILTER_VALIDATE_EMAIL)||!in_array($role,['applicant','employer'],true)||!is_array($profile)||!is_string($profile['name']??null)||trim($profile['name'])===''||mb_strlen($profile['name'])>100||!is_string($hash)||!preg_match('/^\$2y\$12\$[.\/A-Za-z0-9]{53}$/D',$hash))throw new BadRequestHttpException('Invalid registration payload');
  if($role==='employer'&&(!in_array($profile['employerType']??null,['company','entrepreneur','private'],true)||!is_string($profile['company']??null)||trim($profile['company'])===''||mb_strlen($profile['company'])>150))throw new BadRequestHttpException('Invalid employer profile');
  $email=strtolower(trim($email));
  return $this->db->transactional(function()use($id,$email,$role,$profile,$hash){
   $this->db->fetchOne('SELECT pg_advisory_xact_lock(hashtextextended(:email, 21))',['email'=>$email]);
   $table=$this->schema->table('users');
   $existing=$this->db->fetchAssociative("SELECT * FROM {$table} WHERE user_uuid=:id OR lower(email)=:email",['id'=>$id,'email'=>$email]);
   if($existing){
    if($existing['user_uuid']!==$id||strtolower($existing['email'])!==$email||$existing['role_code']!==$role)throw new ConflictHttpException('Email belongs to another account');
    return ['success'=>true,'requestId'=>$id,'status'=>'ready'];
   }
   $this->db->executeStatement("INSERT INTO {$table} (user_uuid,email,first_name,last_name,phone_number,verification_channel_id,password_hash,role_code,profile) VALUES (:id,:email,:name,'',NULL,'email',:hash,:role,CAST(:profile AS JSONB))",
    ['id'=>$id,'email'=>$email,'name'=>$profile['name'],'hash'=>$hash,'role'=>$role,'profile'=>json_encode($profile,JSON_THROW_ON_ERROR)]);
   return ['success'=>true,'requestId'=>$id,'status'=>'ready'];
  });
 }
}
