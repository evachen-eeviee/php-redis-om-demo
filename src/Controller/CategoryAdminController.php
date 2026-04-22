<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class CategoryAdminController extends AbstractController
{
    #[Route('/category', name: 'category', methods: ['GET'])]
    public function index(RedisObjectManagerInterface $om): Response
    {
        $category = $om->getRepository(Category::class)->findAll();

        return $this->render('admin/category/category.html.twig', ['category' => $category]);
    }

    #[Route('/category/new', name: 'admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RedisObjectManagerInterface $om): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $om->persist($category);
            $om->flush();
            $this->addFlash('success', 'Catégorie créé !');

            return $this->redirectToRoute('category');
        }

        return $this->render('admin/category/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/category/edit/{id}', name: 'category_edit', methods: ['GET'])]
    public function edit(Request $request, RedisObjectManagerInterface $om, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om->flush();

            return $this->redirectToRoute('category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category/categoryedit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete', methods: ['POST', 'DELETE'])]
    public function delete(string $id, RedisObjectManagerInterface $om): Response
    {
        $category = $om->getRepository(Category::class)->find($id);

        if ($category) {
            $om->remove($category);
            $om->flush();

            $this->addFlash('success', 'La catégorie a été supprimé définitivement.');
        } else {
            $this->addFlash('error', 'La catégorie n\'a pas pu être supprimer');
        }

        return $this->redirectToRoute('category', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/category/{id}', name: 'category_show', methods: ['GET'])]
    public function show(string $id,Category $category, RedisObjectManagerInterface $om): Response
    {

        $category = $om->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        $books = $om->getRepository(Book::class)->findBy(['category' => $category]);

        return $this->render('admin/category/categoryshow.html.twig', [
            'category' => $category,
            'books' => $books,
        ]);
    }
}
