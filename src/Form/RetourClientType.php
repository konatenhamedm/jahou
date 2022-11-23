<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Fournisseur;
use App\Entity\RetourClient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetourClientType extends AbstractType
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
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('type', ChoiceType::class,
                [
                    'placeholder' => 'Choisir un type de vente',
                    'label' => 'Privilèges Supplémentaires',
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2 type'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'DIRECTE' => 'Vente directe',
                        'INDIRECTE' => 'Vente indirect'
                    ]),
                ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'FullName',
                'label' => 'Client',
                'attr' => ['class' => 'has-select2 article']
            ])
            ->add('total_montant',null,[
                'label'=>false,
                'attr' => ['class' => 'total_montant'],
                'required' => false,
                'attr' => ['readonly' => true],
                'mapped'=>false
            ])
            ->add('ligneRetourClients', CollectionType::class, [
                'entry_type' => LigneRetourClientType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ])

            ->add('transition',SubmitType::class,['label' => 'Valider le rétour', 'attr' => ['class' => 'btn btn-primary btn-sm btn-ajax']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RetourClient::class,
        ]);
    }
}
