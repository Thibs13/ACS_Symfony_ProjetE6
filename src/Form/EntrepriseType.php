<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ENT_Nom', null, ['label' => 'Nom'])
            ->add('ENT_Telephone', null, ['label' => 'Téléphone'])
            ->add('ENT_Email', null, ['label' => 'Email'])
            ->add('ENT_Adresse', null, ['label' => 'Adresse'])
            ->add('VIL_ID', EntityType::class, [
                'class' => Ville::class,
                'label' => 'Ville',
                'choice_label' => 'VIL_Nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
