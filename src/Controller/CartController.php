<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
        ]);
    }
    
    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Product $product, Request $request, CartService $cartService): Response
    {
        $quantity = $request->query->getInt('quantity', 1);
        
        if (!$product->IsActive() || $product->getStock() < $quantity) {
            $this->addFlash('danger', 'Product not available in the requested quantity.');
            return $this->redirectToRoute('app_product_show', ['slug' => $product->getSlug()]);
        }
        
        $cartService->add($product->getId(), $quantity);
        
        $this->addFlash('success', 'Product added to cart!');
        
        // Check if request is AJAX
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'cartCount' => $cartService->getCartCount(),
            ]);
        }
        
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease(int $id, CartService $cartService): Response
    {
        $cartService->decrease($id);
        
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/clear', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        
        return $this->redirectToRoute('app_cart');
    }
}