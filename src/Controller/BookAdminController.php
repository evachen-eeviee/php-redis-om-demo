<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class BookAdminController extends AbstractController
{
    #[Route('/admin/books', name: 'admin_index_books', methods: ['GET'])]
    public function index(RedisObjectManagerInterface $om): Response
    {
        $books = $om->getRepository(Book::class)->findAll();

        return $this->render('admin/book/index.html.twig', ['books' => $books]);
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

    #[Route('/books/edit/{id}', name: 'admin_book_edit', methods: ['GET'])]
    public function edit(Request $request, RedisObjectManagerInterface $om, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->flush();

            return $this->redirectToRoute('admin_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/books/{id}', name: 'admin_book_delete', methods: ['DELETE'])]
    public function delete(Request $request, RedisObjectManagerInterface $om, Book $book): Response
    {
        if ($this->isCsrfTokenValid('book_delete', $request->request->get('_token'))) {
            $om->remove($book);
            $om->flush();
        }

        return $this->redirectToRoute('admin_book_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/books/{id}', name: 'admin_book_show', methods: ['GET'])]
    public function show(int $id, RedisObjectManagerInterface $om): Response
    {
        $book = $om->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        return $this->render('main/show.html.twig', [
            'book' => $book,
        ]);
    }
}
