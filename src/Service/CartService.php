<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $productRepository;
    
    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }
    
    /**
     * Get the session cart data
     */
    private function getCart(): array
    {
        return $this->requestStack->getSession()->get('cart', []);
    }
    
    /**
     * Save the cart to session
     */
    private function saveCart(array $cart): void
    {
        $this->requestStack->getSession()->set('cart', $cart);
    }
    
    /**
     * Add a product to the cart
     */
    public function add(int $id, int $quantity = 1): void
    {
        $cart = $this->getCart();
        
        if (!empty($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }
        
        $this->saveCart($cart);
    }
    
    /**
     * Remove a product from the cart
     */
    public function remove(int $id): void
    {
        $cart = $this->getCart();
        
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        
        $this->saveCart($cart);
    }
    
    /**
     * Decrease the quantity of a product in cart
     */
    public function decrease(int $id): void
    {
        $cart = $this->getCart();
        
        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }
        
        $this->saveCart($cart);
    }
    
    /**
     * Get the cart with full product details
     */
    public function getFullCart(): array
    {
        $cart = $this->getCart();
        $fullCart = [];
        
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            
            if ($product) {
                $fullCart[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            } else {
                // Product doesn't exist, remove it from the cart
                $this->remove($id);
            }
        }
        
        return $fullCart;
    }
    
    /**
     * Get the total price of the cart
     */
    public function getTotal(): float
    {
        $total = 0;
        
        foreach ($this->getFullCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Get the number of items in the cart
     */
    public function getCartCount(): int
    {
        $count = 0;
        $cart = $this->getCart();
        
        foreach ($cart as $quantity) {
            $count += $quantity;
        }
        
        return $count;
    }
    
    /**
     * Clear the cart
     */
    public function clear(): void
    {
        $this->saveCart([]);
    }
}