<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\PurchaseType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[ Route( '/purchase' ) ]

class PurchaseController extends AbstractController {
    #[ Route( '/checkout', name: 'app_purchase_checkout' ) ]

    public function checkout( Request $request, CartService $cartService, ProductRepository $productRepository, EntityManagerInterface $entityManager ): Response {
        // Redirect if cart is empty
        if ( count( $cartService->getFullCart() ) === 0 ) {
            $this->addFlash( 'warning', 'Your cart is empty!' );
            return $this->redirectToRoute( 'app_cart' );
        }

        // Only logged in users can checkout
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ( !$user ) {
            $this->addFlash( 'warning', 'Please log in to checkout.' );
            return $this->redirectToRoute( 'app_login' );
        }

        $purchase = new Purchase();
        $purchase->setUser( $user );
        $purchase->setPurchaseDate( new \DateTimeImmutable() );
        $purchase->setStatus( 'pending' );

        // Set default shipping address from user
        if ( $user->getAddress() ) {
            $purchase->setShippingAddress( $user->getAddress() );
        }

        $purchase->setPayementMethode( 'credit_card' );
        // Default

        $form = $this->createForm( PurchaseType::class, $purchase );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $cartItems = $cartService->getFullCart();
            $total = 0;

            // Create purchase items and update product stock
            foreach ( $cartItems as $item ) {
                $product = $productRepository->find( $item[ 'product' ]->getId() );

                // Check if product is still available and has enough stock
                if ( !$product->isIsActive() || $product->getStock() < $item[ 'quantity' ] ) {
                    $this->addFlash( 'danger', 'Product ' . $product->getName() . ' is no longer available in the requested quantity.' );
                    return $this->redirectToRoute( 'app_cart' );
                }

                $purchaseItem = new PurchaseItem();
                $purchaseItem->setPurchase( $purchase );
                $purchaseItem->setProduct( $product );
                $purchaseItem->setQuantity( $item[ 'quantity' ] );
                $purchaseItem->setPrice( $product->getPrice() );

                $entityManager->persist( $purchaseItem );

                // Update stock
                $product->setStock( $product->getStock() - $item[ 'quantity' ] );
                $entityManager->persist( $product );

                // Calculate total
                $total += $product->getPrice() * $item[ 'quantity' ];
            }

            $purchase->setTotalAmount( $total );
            $entityManager->persist( $purchase );
            $entityManager->flush();

            // Clear the cart
            $cartService->clear();

            $this->addFlash( 'success', 'Your order has been placed successfully!' );
            return $this->redirectToRoute( 'app_purchase_success', [ 'id' => $purchase->getId() ] );
        }

        return $this->render( 'purchase/checkout.html.twig', [
            'form' => $form->createView(),
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
        ] );
    }

    #[ Route( '/success/{id}', name: 'app_purchase_success' ) ]

    public function success( Purchase $purchase ): Response {
        // Check if purchase belongs to current user
        if ( $purchase->getUser() !== $this->getUser() ) {
            throw $this->createAccessDeniedException( 'You cannot access this purchase.' );
        }

        return $this->render( 'purchase/success.html.twig', [
            'purchase' => $purchase,
        ] );
    }

    #[ Route( '/history', name: 'app_purchase_history' ) ]

    public function history(): Response {
        // Only logged in users can see their purchase history
        if ( !$this->getUser() ) {
            return $this->redirectToRoute( 'app_login' );
        }
        /** @var \App\Entity\User $temp */
        $temp = $this->getUser();
        return $this->render( 'purchase/history.html.twig', [
            'purchases' => ( $temp instanceof \App\Entity\User ) ? $temp->getPurchases() : [],
        ] );
    }

    #[ Route( '/detail/{id}', name: 'app_purchase_detail' ) ]

    public function detail( Purchase $purchase ): Response {
        // Check if purchase belongs to current user
        if ( $purchase->getUser() !== $this->getUser() ) {
            throw $this->createAccessDeniedException( 'You cannot access this purchase.' );
        }

        return $this->render( 'purchase/detail.html.twig', [
            'purchase' => $purchase,
        ] );
    }
}

