<?php
namespace App\Entity;
use Talleu\RedisOm\Om\Mapping as RedisOm;

#[RedisOm\Entity]
class Category{

    #[RedisOm\Id]
    #[RedisOm\Property]
    public int $id;

    #[RedisOm\Property]
    public string $category;
}
