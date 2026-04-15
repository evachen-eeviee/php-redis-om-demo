<?php

namespace App\Controller;

use App\Entity\category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManager;

class CategoryAdminController extends AbstractController{
    #[Route('/category', name: 'admin_index_category', methods: ['GET'])]
    public function index(RedisObjectManager $om): Response{
        $category = $om->getRepository(category::class)->findAll();
        return $this->render('admin/category/index.html.twig', ['category' => $category]);
    }

    #[Route('/category/new', name: 'admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManager $om) : Response{
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->persist($category);
            $om->flush();
            $this->addFlash('success', 'Livre créé !');
            return $this->redirectToRoute('admin_category_index');
        }
        return $this->render('admin/category/new.html.twig', [$form, $form->createView()]);
    }


    #[Route('/category/{id}', name: 'admin_category_edit', methods: ['GET'])]
    public function edit(Request $request, RedisObjectManager $om, Category $category) : Response{
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $om->flush();
            return $this->redirectToRoute('admin_category_index',[], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}', name: 'admin_category_delete', methods: ['DELETE'])]
    public function delete(Request $request, RedisObjectManager $om, Category $category) : Response
    {
        if($this->isCsrfTokenValid('category_delete', $request->request->get('_token'))){
            $om->remove($category);
            $om->flush();
        }

        return $this->redirectToRoute('admin_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/category/{id}', name: 'admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response{
        return $this->render('admin/category/show.html.twig', ['category' => $category]);
    }

}
