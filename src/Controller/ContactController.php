<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController {
    #[ Route( '/contact', name: 'contact' ) ]

    public function index( Request $request ): Response {
        $form = $this->createForm( ContactType::class );
        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->addFlash( 'notice', 'Message saved successfully 🙂' );

            $datas = $form->getData();
            $content = "From: {$datas['firstname']} {$datas['lastname']}\n"
            . "Email: {$datas['email']}\n"
            . "Message: {$datas['content']}\n"
            . '--------------------------\n';

            // Path to the local log file ( relative to project root or absolute )
            $logFile = $this->getParameter( 'kernel.project_dir' ) . '/var/log/contact_messages.log';

            // Append to file
            file_put_contents( $logFile, $content, FILE_APPEND );
        }

        return $this->render( 'contact/index.html.twig', [
            'form' => $form->createView(),
        ] );
    }
}
