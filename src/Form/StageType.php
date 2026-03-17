<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Etudiant;
use App\Entity\Stage;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('STA_DateDebut')
            ->add('STA_DateFin')
            ->add('STA_Remarque')
            ->add('STA_Remerciement')
            ->add('STA_Bilan')
            ->add('STA_Attestation')
            ->add('STA_Jury')
            ->add('STA_Commentaire')
            ->add('STA_DateRetenu')
            ->add('ETU_ID', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'id',
            ])
            ->add('ENT_ID', EntityType::class, [
                'class' => Entreprise::class,
                'choice_label' => 'id',
            ])
            ->add('EnseignantVisite', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
            ->add('EnseignantSuivi', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stage::class,
        ]);
    }
}
