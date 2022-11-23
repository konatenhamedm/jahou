<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\LigneSortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite',IntegerType::class,[
                'label'=>false,
                'required' => true,
                'attr' => ['class' => 'quantite','step'=>1,'min'=>1],
            ])
            ->add('remise',IntegerType::class,[
                'label'=>false,
                'attr' => ['class' => 'remise','step'=>1,'min'=>0],
                'required' => true
            ])
            ->add('montant',null,[
                'label'=>false,
                'attr' => ['class' => 'montant'],
                'required' => false,
                 'attr' => ['readonly' => true],
                'mapped'=>false
            ])
            ->add('total',null,[
                'label'=>false,
                'attr' => ['class' => 'total'],
                'required' => false,
                'mapped'=>false,
                 'attr' => ['readonly' => true],
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'designation',
                'label'=>false,
                'attr' => ['class' => 'has-select2 article']
            ])
            //->add('mouvementSortie')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneSortie::class,
        ]);
    }
}
