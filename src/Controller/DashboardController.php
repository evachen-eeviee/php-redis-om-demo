<?php


namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talleu\RedisOm\Om\RedisObjectManagerInterface;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(RedisObjectManagerInterface $om): Response
    {
        // On récupère et compte les enregistrements pour chaque entité
        $books = $om->getRepository(Book::class)->findAll();
        $category = $om->getRepository(Category::class)->findAll();
        $users = $om->getRepository(User::class)->findAll();
        $comments = $om->getRepository(Comment::class)->findAll();

        // On prépare un tableau pour faciliter l'affichage dans Twig
        $stats = [
            [
                'name' => 'Livres',
                'count' => $books,
                'url' => '/admin/books',
                'icon' => '📚'
            ],
            [
                'name' => 'Catégories',
                'count' => $category,
                'url' => '/category',
                'icon' => '🏷️'
            ],
            [
                'name' => 'Utilisateurs',
                'count' => $users,
                'url' => '/user',
                'icon' => '👥'
            ],
            [
                'name' => 'Commentaires',
                'count' => $comments,
                'url' => '/comment',
                'icon' => '💬'
            ],
        ];

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
