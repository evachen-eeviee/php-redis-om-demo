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
    public function index(Request $request, RedisObjectManagerInterface $om): Response
    {


        $repository = $om->getRepository(User::class);

        $page = $request->query->getInt('page', 1);

        $paginator = $repository->paginate(
            criteria: [],
            page: $page,
            itemsPerPage: 8,
        );

        return $this->render('admin/user/user.html.twig', [
            'paginator' => $paginator,
            'users' => $paginator->getItems(),
        ]);
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

            return $this->redirectToRoute('user');
        }

        return $this->render('user/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/justine', name: 'user_justine', methods: ['GET', 'POST'])]
    public function oneJustine(RedisObjectManagerInterface $om): Response{
        $userRepo = $om->getRepository(User::class);
        $user = $userRepo->findOneBy(['name' => 'Justine']);
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur nommé Justine trouvé.');
        }
        $users = $userRepo->findMultiple([1862976071218001,1863085144589098,1862976157719200]);

        return $this->render('admin/user/justine.html.twig', [
            'users' => $users,
            'user' => $user,
        ]);
    }

    #[Route('/user/edit/{id}', name: 'user_edit', methods: ['POST', 'GET'])]
    public function edit(string $id, Request $request, RedisObjectManagerInterface $om): Response
    {
        $user = $om->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->merge($user);
            $om->flush();

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/useredit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST', 'DELETE'])]
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

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
