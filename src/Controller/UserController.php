<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManager;

class UserController extends AbstractController{

    #[Route('/user', name: 'admin_user', methods: ['GET', 'POST'])]
    public function index(RedisObjectManager $om): Response{
        $users = $om->getRepository(User::class)->findAll();
        return $this->render('admin/user/index.html.twig', ['users' => $users]);
    }

    #[Route('/user/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManager $om){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($user);
            $om->flush();
            $this->addFlash('success', 'User créé !');
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/new.html.twig', [$form, $form->createView()]);
    }

    public function edit(Request $request, RedisObjectManager $om, User $user){
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->flush();
            return $this->redirectToRoute('admin_user_index',[], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, RedisObjectManager $om, User $user) : Response
    {
        if($this->isCsrfTokenValid('user_delete', $request->request->get('_token'))){
            $om->remove($user);
            $om->flush();
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/users/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(User $user): Response{
        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }
}
