<?php

namespace App\Form;

use App\Entity\Promotions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class PromotionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pro_libelle', null, ['label' => 'Classe'])
            ->add('pro_session', null, ['label' => 'Session'])
            ->add('pro_datedebut', DateType::class, [
                'label' => 'Date de début de stage',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('pro_datefin', DateType::class, [
                'label' => 'Date de fin de stage',
                'widget' => 'single_text',
                'html5' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotions::class,
        ]);
    }
}
