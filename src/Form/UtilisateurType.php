<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Classification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',null, array('label' => false))
           // ->add('roles')
           ->add('classifications', EntityType::class, [
            // looks for choices from this entity
            'class' => Classification::class,

            // uses the User.username property as the visible option string
            'choice_label' => 'type',

            // used to render a select box, check boxes or radios
             'multiple' => true,
             'expanded' => true,
             
              
        ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => false],
                'second_options' => ['label' => false],
            ])
            ->add('email',EmailType::class, array('label' => false))
            ->add('imageFile', FileType::class, [
                

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
               
            ])
            ->add('adresse')
            ->add('nomAssociation')
            ->add('telephone')
            
            
            ->add('submit',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
