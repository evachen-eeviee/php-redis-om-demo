<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
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

    #[RedisOm\Property(index: true)]
    public bool $enabled;

    #[RedisOm\Property(index: true)]
    public Category $category;

    #[RedisOm\Property(index: true)]
    public float $price;

    #[RedisOm\Property(index: true)]
    public Assert\Date $publishedAt;

}
