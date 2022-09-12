<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class ContactType extends AbstractType
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
                    'class' =>'form-label mt-4'
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
                    'class' =>'form-label mt-4'
                ]
            ])
            ->add('subjet', TextType::class, [
                'attr'=>[
                    'class'=> 'form-control',
                    'minlenght' => '2',
                    'maxlenght' => '100'
                ],
                'label' => 'sujet',
                'label_attr' => [
                    'class' =>'form-control'
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
                'label' => 'message',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' =>'Soumettre ma demande'
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
