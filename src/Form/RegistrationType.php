<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr'=>[
                    'class'=> 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '50'
                ],
                'label' => 'Nom / Prénom',
                'label_attr' => [
                    'class' =>'form-control'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr'=>[
                    'class'=> 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '50'
                ],
                'required' => false,
                'label' => 'Pseudo (falcultatif)',
                'label_attr' => [
                    'class' =>'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr'=>[
                    'class'=> 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '180'
                ],
                'label' => 'Adresse Email',
                'label_attr' => [
                    'class' =>'form-control'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr'=>[
                        'class'=> 'form-control'
                    ],
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' =>'form-label'
                    ]
                ],
                'second_options' => [
                    'attr'=>[
                        'class'=> 'form-control'
                    ],
                    'label' => 'Confirmation mot de passe',
                    'label_attr' => [
                        'class' =>'form-label'
                    ]
                ],
                'invalid_message' => 'Les deux mots de passe ne correspondent pas.'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
