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
        if ($search->unavailable) {
            $criteria['enabled'] = false;
        }
        if($search->enum){
            $criteria['bookEnum'] = $search->enum->value;
        }

        if (!empty($criteria)) {
            $results = $this->repository->findBy($criteria, ['price' => 'ASC']);
        } else {
            $results = $this->repository->findAll();
        }
        $books = iterator_to_array($results);





        if (null !== $search->priceMin || null !== $search->priceMax) {
            $results = $this->repository->findBy(['price' => ['$gte' => $search->priceMin, '$lte' => $search->priceMax]]);
            $books = iterator_to_array($results);
            $books = array_values($books);
        }

        return $books;
    }
}
