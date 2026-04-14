<?php
namespace App\Controller;


use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManager;

class CommentAdminController extends AbstractController{
    #[Route('/comments', name: 'admin_index_comments', methods: ['GET'])]
    public function index(RedisObjectManager $om): Response{
        $Comments = $om->getRepository(Comment::class)->findAll();
        return $this->render('admin/comment/index.html.twig', ['comments' => $Comments]);
    }

    #[Route('/comments/new', name: 'admin_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManager $om){
        $Comment = new Comment();
        $form = $this->createForm(CommentType::class, $Comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($Comment);
            $om->flush();
            $this->addFlash('success', 'Livre créé !');
            return $this->redirectToRoute('admin_comment_index');
        }
        return $this->render('admin/comment/new.html.twig', [$form, $form->createView()]);
    }


    #[Route('/comments/{id}', name: 'admin_comment_edit', methods: ['GET'])]
    public function edit(Request $request, RedisObjectManager $om, Comment $comment){
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->flush();
            return $this->redirectToRoute('admin_comment_index',[], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comments/{id}', name: 'admin_comment_delete', methods: ['DELETE'])]
    public function delete(Request $request, RedisObjectManager $om, Comment $Comment) : Response
    {
        if($this->isCsrfTokenValid('comment_delete', $request->request->get('_token'))){
            $om->remove($Comment);
            $om->flush();
        }

        return $this->redirectToRoute('admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/comments/{id}', name: 'admin_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response{
        return $this->render('admin/comment/show.html.twig', ['comment' => $comment]);
    }

}
