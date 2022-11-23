<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\LigneRetourFournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneRetourFournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite',IntegerType::class,[
                'label' => false,
                'attr' => ['class' => 'quantite','step'=>1,'min'=>1],
                'required' => true
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'designation',
                'label' => false,
                'attr' => ['class' => 'has-select2 article']
            ])
            //->add('retourFournisseur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneRetourFournisseur::class,
        ]);
    }
}
