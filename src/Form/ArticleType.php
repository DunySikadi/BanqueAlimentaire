<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Classification;
use App\Entity\SousFamille;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeArticle')
            ->add('libelleArticle')
            ->add('indice')
            ->add('sousFamille', EntityType::class, [
                // looks for choices from this entity
                'class' => SousFamille::class,
    
                // uses the User.username property as the visible option string
                'choice_label' => 'libelleSF',
    
                // used to render a select box, check boxes or radios
                 //'multiple' => true,
                 //'expanded' => true,
                 
                  
            ])
            ->add('classification', EntityType::class, [
                // looks for choices from this entity
                'class' => Classification::class,
    
                // uses the User.username property as the visible option string
                'choice_label' => 'type',
    
                // used to render a select box, check boxes or radios
                 //'multiple' => true,
                 //'expanded' => true,
                 
                  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
