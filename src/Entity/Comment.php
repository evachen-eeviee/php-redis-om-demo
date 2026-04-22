<?php

namespace App\Entity;

use Talleu\RedisOm\Om\Mapping as RedisOm;
use Talleu\RedisOm\Om\RedisFormat;

#[RedisOm\Entity(
    format : RedisFormat::JSON->value,
)]
class Comment
{
    #[RedisOm\Id]
    #[RedisOm\Property]
    public ?int $id = null;

    #[RedisOm\Property(index: true)]
    public User $author;

    #[RedisOm\Property(index: true)]
    public Book $book;
    #[RedisOm\Property]
    public string $content;

    #[RedisOm\Property]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
