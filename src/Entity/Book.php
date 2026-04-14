<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\Date;
use Talleu\RedisOm\Om\Mapping as RedisOm;


#[RedisOm\Entity]
class Book{

    #[RedisOm\Id]
    #[RedisOm\Property]
    public int $id;

    #[RedisOm\Property(index: true)]
    public string $title;

    #[RedisOm\Property(index: true)]
    public User $author;

    #[RedisOm\Property]
    public string $description;

    #[RedisOm\Property]
    public bool $enabled;

    #[RedisOm\Property(index: true)]
    public Category $cateory;

    #[RedisOm\Property]
    public float $price;

    #[RedisOm\Property]
    public Date $publishedAt;

}
