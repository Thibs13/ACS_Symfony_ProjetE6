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
            ->add('STA_DateDebut', null, ['label' => 'Date de début'])
            ->add('STA_DateFin', null, ['label' => 'Date de fin'])
            ->add('STA_Remarque', null, ['label' => 'Remarque'])
            ->add('STA_Remerciement', null, ['label' => 'Remerciement'])
            ->add('STA_Bilan', null, ['label' => 'Bilan'])
            ->add('STA_Attestation', null, ['label' => 'Attestation'])
            ->add('STA_Jury', null, ['label' => 'Jury'])
            ->add('STA_Commentaire', null, ['label' => 'Commentaire'])
            ->add('STA_DateRetenu', null, ['label' => 'Date retenu'])
            ->add('ETU_ID', EntityType::class, [
                'class' => Etudiant::class,
                'label' => 'Étudiant',
                'choice_label' => 'id',
            ])
            ->add('ENT_ID', EntityType::class, [
                'class' => Entreprise::class,
                'label' => 'Entreprise',
                'choice_label' => 'id',
            ])
            ->add('EnseignantVisite', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Enseignant de visite',
                'choice_label' => 'id',
            ])
            ->add('EnseignantSuivi', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Enseignant de suivi',
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
