<?php

namespace App\Entity;

use Talleu\RedisOm\Om\Mapping as RedisOm;
use Talleu\RedisOm\Om\RedisFormat;

#[RedisOm\Entity(
    format: RedisFormat::JSON->value
)]
class Book
{
    #[RedisOm\Id]
    #[RedisOm\Property]
    public ?int $id = null;

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
    public \DateTimeImmutable $publishedAt;
}
