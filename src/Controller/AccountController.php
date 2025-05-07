<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * member space 
 */
class AccountController extends AbstractController
{
    #[Route('/compte', name: 'account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
        ]);
    }

    /**
     *  allow the modification of the password
     */
    #[Route('/compte/password', name: 'account_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            $new_password = $form->get('new_password')->getData();
            $isOldPasswordValid = $passwordHasher->isPasswordValid($user, $old_password);
            if ($isOldPasswordValid) {
                $password = $passwordHasher->hashPassword($user,$new_password);
                $user->setPassword($password);
                $em->flush();
                $this->addFlash(
                    'notice', 
                    'password modified successfully :)'
                );
                return $this->redirectToRoute('account');
            } else {
                $this->addFlash(
                    'notice', 
                    'current password is wrong:('
                );
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * show all the current orders of the user
     */
    #[Route('/compte/commandes', name: 'account_orders')]
    public function showOrders(OrderRepository $repository): Response
    {
        $orders = $repository->findPaidOrdersByUser($this->getUser());
        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * show a specific order
     */
    #[Route('/compte/commandes/{reference}', name: 'account_order')]
    public function showOrder(Order $order): Response
    {
        if (!$order || $order->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('Commande innaccessible');
        }
        return $this->render('account/order.html.twig', [
            'order' => $order
        ]);
    }
}
