<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManager;

class BookAdminController extends AbstractController
{
    #[Route('/books', name: 'admin_index_books', methods: ['GET'])]
    public function index(RedisObjectManager $om): Response{
        $books = $om->getRepository(Book::class)->findAll();
        return $this->render('admin/book/index.html.twig', ['books' => $books]);
    }

    #[Route('/books/new', name: 'admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManager $om){
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($book);
            $om->flush();
            $this->addFlash('success', 'Livre créé !');
            return $this->redirectToRoute('admin_book_index');
        }
        return $this->render('admin/book/new.html.twig', [$form, $form->createView()]);
    }


    #[Route('/books/{id}', name: 'admin_book_edit', methods: ['GET'])]
    public function edit(Request $request, RedisObjectManager $om, Book $book){
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->flush();
            return $this->redirectToRoute('admin_book_index',[], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/books/{id}', name: 'admin_book_delete', methods: ['DELETE'])]
    public function delete(Request $request, RedisObjectManager $om, Book $book) : Response
    {
        if($this->isCsrfTokenValid('book_delete', $request->request->get('_token'))){
            $om->remove($book);
            $om->flush();
        }

        return $this->redirectToRoute('admin_book_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/books/{id}', name: 'admin_book_show', methods: ['GET'])]
    public function show(Book $book): Response{
        return $this->render('admin/book/show.html.twig', ['book' => $book]);
    }


}
