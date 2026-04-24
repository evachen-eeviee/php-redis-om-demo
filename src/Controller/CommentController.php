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

    #[Route('/comment', name: 'comment', methods: ['GET', 'POST'])]
    public function index(RedisObjectManagerInterface $om, Request $request): Response
    {
        $limit = 10;
        $currentPage = max(1, $request->query->getInt('page', 1));
        $comment = $om->getRepository(Comment::class)->findBy([]);
        $totalComment = count($comment);
        $totalPages = ceil($totalComment / $limit);

        $offset = ($currentPage - 1) * $limit;
        $commentsPage = array_slice($comment, $offset, $limit);

        return $this->render('admin/comment/comment.html.twig', [
            'comments' => $commentsPage,
            'total_pages' => $totalPages,
            'current_page' => $currentPage,
        ]);
    }

    #[Route('/comment/edit/{id}', name: 'comment_edit', methods: ['POST', 'GET'])]
    public function edit(string $id, Request $request, RedisObjectManagerInterface $om, Comment $comment): Response
    {
        $comment = $om->getRepository(Comment::class)->find($id);

        $form = $this->createForm(CommentType::class, $comment, [
            'authors' => (array) $om->getRepository(User::class)->findBy([]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->merge($comment);
            $om->flush();

            return $this->redirectToRoute('comment', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/comment/commentedit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/delete/{id}', name: 'comment_delete', methods: ['POST', 'DELETE'])]
    public function delete(string $id, Request $request, RedisObjectManagerInterface $om, Comment $comment): Response
    {
        $comment = $om->getRepository(Comment::class)->find($id);

        if ($comment) {
            $om->remove($comment);
            $om->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé définitivement.');
        } else {
            $this->addFlash('error', 'Le commentaire n\'a pas pu être supprimer');
        }

        return $this->redirectToRoute('comment', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('books/{id}', name: 'admin_book_show', methods: ['GET', 'POST'])] // On réutilise le nom de route attendu
    public function show(string $id, Request $request, RedisObjectManagerInterface $om): Response
    {
        $book = $om->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $limit = 5;
        $currentPage = max(1, $request->query->getInt('page', 1));

        $allComments = $om->getRepository(Comment::class)->findBy(['book_id' => $book->id]);
        $totalComments = count($allComments);
        $totalPages = ceil($totalComments / $limit);

        $offset = ($currentPage - 1) * $limit;
        $commentsPage = array_slice($allComments, $offset, $limit);

        $comment = new Comment();
        if (!$book instanceof Book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }
        $comment->book = $book;

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
            'comments' => $commentsPage,
            'total_pages' => $totalPages,
            'current_page' => $currentPage,
            'form' => $form->createView(),
        ]);
    }
}
