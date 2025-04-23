<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index')]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $productRepository->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
            
        $products = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12 // 12 products per page
        );
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    #[Route('/{slug}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        // Check if product is active
        if (!$product->IsActive()) {
            throw $this->createNotFoundException('Product not found');
        }
        
        // Get related products from the same category
        $relatedProducts = $product->getCategory()->getProducts()->filter(function($p) use ($product) {
            return $p->getId() !== $product->getId() && $p->isIsActive();
        })->slice(0, 4); // Limit to 4 related products
        
        return $this->render('product/show.html.twig', [            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
    
    #[Route('/search', name: 'app_product_search')]
    public function search(Request $request, ProductRepository $productRepository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q', '');
        $categoryId = $request->query->get('category', null);
        
        $queryBuilder = $productRepository->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->setParameter('active', true);
            
        if (!empty($query)) {
            $queryBuilder->andWhere('p.name LIKE :query OR p.description LIKE :query OR p.brand LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }
        
        if ($categoryId) {
            $queryBuilder->andWhere('p.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        
        $queryBuilder->orderBy('p.createdAt', 'DESC');
        
        $products = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            12 // 12 products per page
        );
        
        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query,
            'categoryId' => $categoryId,
        ]);
    }
}