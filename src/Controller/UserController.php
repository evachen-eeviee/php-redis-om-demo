<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user', methods: ['GET', 'POST'])]
    public function index(RedisObjectManagerInterface $om): Response
    {
        $users = $om->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    #[Route('/user/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManagerInterface $om): Response
    {
        // $user = new User();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $om->persist($user);
            $om->flush();
            $this->addFlash('success', 'User créé !');

            return $this->redirectToRoute('admin_index_books');
        }

        return $this->render('user/new.html.twig', ['form' => $form->createView()]);
    }

    public function edit(Request $request, RedisObjectManagerInterface $om, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->flush();

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'admin_book_delete', methods: ['POST', 'DELETE'])]
    public function delete(string $id, Request $request, RedisObjectManagerInterface $om, User $user): Response
    {
        $user = $om->getRepository(User::class)->find($id);

        if ($user) {
            $om->remove($user);
            $om->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé définitivement.');
        } else {
            $this->addFlash('error', 'L\'utilisateur n\'a pas pu être supprimer');
        }

        return $this->redirectToRoute('admin_index_books', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/users/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }
}
