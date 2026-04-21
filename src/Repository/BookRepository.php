<?php

namespace App\Repository;

use App\Entity\Book;
use App\Model\SearchData;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class BookRepository
{
    private $repository;

    public function __construct(RedisObjectManagerInterface $om)
    {
        // On récupère le repository natif du bundle
        $this->repository = $om->getRepository(Book::class);
    }

    public function findBySearch(SearchData $search): array
    {
        $criteria = [];

        if (!empty($search->title)) {
            $criteria['title'] = $search->title;
        }
        if (null !== $search->author) {
            $criteria['author'] = $search->author;
        }
        if (null !== $search->category) {
            $criteria['category'] = $search->category;
        }


        if (!empty($criteria)) {
            $results = $this->repository->findBy($criteria);
        } else {
            $results = $this->repository->findAll();
        }

        $books = iterator_to_array($results);

        if (null !== $search->priceMin || null !== $search->priceMax) {
            $books = array_filter($books, function (Book $book) use ($search) {
                $price = $book->price;
                $minOk = null === $search->priceMin || $price >= $search->priceMin;
                $maxOk = null === $search->priceMax || $price <= $search->priceMax;

                return $minOk && $maxOk;
            });
        }

        return $books;
    }
}
