<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Modele;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation')
            ->add('seuil',IntegerType::class,[

            ])
            ->add('prixVente',MoneyType::class,[

            ])
            ->add('prixAchat',MoneyType::class,[

            ])
            ->add('quantite',IntegerType::class,[

            ])
            ->add('modele', EntityType::class, [
                'class' => Modele::class,
                'choice_label' => 'libelle',
                'label' => 'Modele',
                'attr' => ['class' => 'has-select2 article']
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
