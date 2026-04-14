<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\Date;
use Talleu\RedisOm\Om\Mapping as RedisOm;

#[RedisOm\Entity]
class Comment{

    #[RedisOm\Id]
    #[RedisOm\Property]
    public int $id;

    #[RedisOm\Property(index: true)]
    public User $author;

    #[RedisOm\Property(index: true)]
    public Book $book;
    #[RedisOm\Property]
    public string $content;

    #[RedisOm\Property]
    public Date $createdAt;
}
