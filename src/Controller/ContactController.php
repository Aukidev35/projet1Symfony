<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(Request $request, 
    EntityManagerInterface $manager,
    MailService $mailService
    ): Response
    {
        $contact = new Contact();
        if ($this->getUser()) 
        {
            $contact->setFullName($this->getUser()->getFullName())
                    ->setEmail($this->getUser()->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData(); 

            $manager->persist($contact);
            $manager->flush();

            // email
            $mailService->sendEmail(
                $contact->getEmail(),
                $contact->getSubject(),
                'email/contact.html.twig',
                ['contact' => $contact]
            );


            $this->addFlash(
                'success',
                'Votre demande de contact est envoyé!'
            );

            return $this->redirectToRoute('contact.index');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
