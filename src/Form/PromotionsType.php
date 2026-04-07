<?php

namespace App\Form;

use App\Entity\Promotions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pro_libelle', null, ['label' => 'Classe'])
            ->add('pro_session', null, ['label' => 'Session'])
            ->add('pro_datedebut', null, ['label' => 'Date de début de stage'])
            ->add('pro_datefin', null, ['label' => 'Date de fin de stage'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotions::class,
        ]);
    }
}
