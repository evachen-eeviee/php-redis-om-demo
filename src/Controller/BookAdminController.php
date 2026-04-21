<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use App\Form\BookType;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class BookAdminController extends AbstractController
{
    #[Route('/admin/books', name: 'admin_index_books', methods: ['GET'])]
    public function index(Request $request, RedisObjectManagerInterface $om, BookRepository $repository): Response
    {
        return $this->extracted($om, $request, $repository);
    }

    #[Route('/books', name: 'index_books', methods: ['GET'])]
    public function indexUser(Request $request, RedisObjectManagerInterface $om, BookRepository $repository): Response
    {
        return $this->extracted($om, $request, $repository);
    }

    #[Route('admin/books/new', name: 'admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManagerInterface $om): Response
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book, [
            'authors' => (array) $om->getRepository(User::class)->findBy([]),
            'categories' => (array) $om->getRepository(Category::class)->findBy([]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->persist($book);
            $om->flush();
            $this->addFlash('success', 'Livre créé !');

            return $this->redirectToRoute('admin_index_books');
        }

        return $this->render('admin/book/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/books/edit/{id}', name: 'admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(string $id, Request $request, RedisObjectManagerInterface $om, Book $book): Response
    {
        $book = $om->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable');
        }
        $form = $this->createForm(BookType::class, $book, [
            'authors' => (array) $om->getRepository(User::class)->findBy([]),
            'categories' => (array) $om->getRepository(Category::class)->findBy([]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->persist($book);
            $om->flush();

            return $this->redirectToRoute('admin_index_books', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/books/delete/{id}', name: 'admin_book_delete', methods: ['POST', 'DELETE'])]
    public function delete(string $id, Request $request, RedisObjectManagerInterface $om, Book $book): Response
    {
        $book = $om->getRepository(Book::class)->find($id);

        if ($book) {
            $om->remove($book);
            $om->flush();

            $this->addFlash('success', 'Le livre a été supprimé définitivement.');
        } else {
            $this->addFlash('error', 'Le livre n\'a pas pu être supprimer');
        }

        return $this->redirectToRoute('admin_index_books', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/books/{id}', name: 'admin_book_show', methods: ['GET'])]
    public function show(string $id, RedisObjectManagerInterface $om): Response
    {
        $book = $om->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        return $this->render('main/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/admin/books', name: 'admin_index_books', methods: ['GET'])]
    public function extracted(Request $request, BookRepository $bookRepo, RedisObjectManagerInterface $om): Response
    {
        $filtre = new SearchData();

        $categories = $om->getRepository(Category::class)->findBy([]);
        $author = $om->getRepository(User::class)->findBy([]);

        $form = $this->createForm(SearchType::class, $filtre, [
            'categories' => $categories,
            'authors' => $author,
        ]);
        $form->handleRequest($request);

        $books = $bookRepo->findBySearch($filtre);

        return $this->render('main/catalogue.html.twig', [
            'books' => $books,
            'form' => $form->createView(),
        ]);
    }
}
