<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class CommentController extends AbstractController
{
    #[Route('books/{id}', name: 'admin_book_show', methods: ['GET', 'POST'])] // On réutilise le nom de route attendu
    public function show(string $id, Request $request, RedisObjectManagerInterface $om): Response
    {
        $book = $om->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $comment = new Comment();
        if (!$book instanceof Book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }
        $comment->book = $book;

        // ATTENTION : Vous devez passer les auteurs au formulaire ici aussi !
        $form = $this->createForm(CommentType::class, $comment, [
            'authors' => (array) $om->getRepository(User::class)->findBy([]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->persist($comment);
            $om->flush();
            $this->addFlash('success', 'Commentaire créé !');
            return $this->redirectToRoute('admin_book_show', ['id' => $id]);
        }

        return $this->render('main/show.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }
}
