<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        // Only logged in users can access their account
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    
    #[Route('/profile', name: 'app_account_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Only logged in users can access their profile
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Your profile has been updated!');
            return $this->redirectToRoute('app_account');
        }
        
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}