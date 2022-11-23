<?php

namespace App\Form;

use App\Entity\Fournisseur;
use App\Entity\RetourFournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetourFournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateRetour', DateType::Class, [
                "label" => "Date achat",
                "required" => false,
                "widget" => 'single_text',
                "input_format" => 'Y-m-d',
                "by_reference" => true,
                "empty_data" => '',
                'attr' => ['class' => 'date']
            ])
            ->add('libelle')
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => 'denominationSocial',
                'label' => 'Fournisseur',
                'attr' => ['class' => 'has-select2 article']
            ])
            ->add('ligneRetourFournisseurs', CollectionType::class, [
                'entry_type' => LigneRetourFournisseurType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('transition',SubmitType::class,['label' => "Valider retour", 'attr' => ['class' => 'btn btn-primary btn-sm btn-ajax']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RetourFournisseur::class,
        ]);
    }
}
