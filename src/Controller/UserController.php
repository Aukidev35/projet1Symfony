<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit',  methods:['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $choosenUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword()))
            {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'les informations ont bien été modifiées'
                );
            }
            else
            {
                $this->addFlash(
                    'warnin',
                    'Le mot de passe renseigné est incorrect'
                );
            }
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods:['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function editPassword(User $choosenUser, 
                                Request $request, 
                                EntityManagerInterface $manager, 
                                UserPasswordHasherInterface $hasher ) : Response
    {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword']))
            {
                $choosenUser->setPassword(
                    $hasher->hashPassword(
                    $choosenUser,
                    $form->getData()['newPassword']
                    )
                );


                $this->addFlash(
                    'success',
                    'le mot de passe à été modifié'
                );

                $manager->persist($choosenUser);
                $manager->flush();
                return $this->redirectToRoute('recipe.index');
            }
            else
            {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }

        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
