<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('denominationSocial', null, ['label' => 'Raison sociale'])
            ->add('ville' ,null, ['label' => 'Ville'])
            ->add('telephone', null, ['label' => 'Télephone'])
            ->add('adresseMail', EmailType::class, ['label' => 'Adresse E-mail', 'required' => false, 'empty_data' => ''])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseur::class,
        ]);
    }
}
