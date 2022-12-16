<?php

namespace App\Form;

use App\Entity\SousFamille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Famille;

class SousFamilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeSF')
            ->add('libelleSF')
            ->add('famille', EntityType::class, [
                // looks for choices from this entity
                'class' => Famille::class,
    
                // uses the User.username property as the visible option string
                'choice_label' => 'libelleFamille',
    
                // used to render a select box, check boxes or radios
                 //'multiple' => true,
                 //'expanded' => true,
                 
                  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SousFamille::class,
        ]);
    }
}
