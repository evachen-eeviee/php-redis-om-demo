<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints\Date;
use Talleu\RedisOm\Om\Mapping as RedisOm;

#[RedisOm\Entity]
class User{

    #[RedisOm\Id]
    #[RedisOm\Property]
    public int $id;

    #[RedisOm\Property(index: true)]
    public string $name;

    #[RedisOm\Property(index: true)]
    public string $email;

    #[RedisOm\Property]
    public int $age;

    #[RedisOm\Property]
    public \DateTimeImmutable $createdAt;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
