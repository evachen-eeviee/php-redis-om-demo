<?php

namespace App\Entity;

//use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Talleu\RedisOm\Om\Mapping as RedisOm;


#[RedisOm\Entity]
class Book{

    #[RedisOm\Id]
    #[RedisOm\Property]
    public int $id;

    #[RedisOm\Property]
    public string $title;

    #[RedisOm\Property]
    public User $author;

    #[RedisOm\Property]
    public string $description;

    #[RedisOm\Property]
    public bool $enabled;

    #[RedisOm\Property]
    public Category $cateory;

    #[RedisOm\Property]
    public float $price;

    #[RedisOm\Property]
    public Date $publishedAt;

}
