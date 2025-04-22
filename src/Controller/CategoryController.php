<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    
    #[Route('/{slug}', name: 'app_category_show')]
    public function show(Category $category, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $paginator->paginate(
            $category->getProducts()->filter(function($product) {
                return $product->isIsActive();
            }),
            $request->query->getInt('page', 1),
            12 // 12 products per page
        );
        
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}