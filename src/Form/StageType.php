<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Etudiant;
use App\Entity\Stage;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('STA_DateDebut', DateType::class, [
                'label' => 'Date de debut*',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('STA_DateFin', DateType::class, [
                'label' => 'Date de fin*',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('STA_Remarque', null, ['label' => 'Remarque'])
            ->add('STA_Remerciement', TextType::class, [
                'label' => 'Remerciement',
                'attr' => [
                    'placeholder' => 'Oui ou Non',
                ],
            ])
            ->add('STA_Bilan', TextType::class, [
                'label' => 'Bilan',
                'attr' => [
                    'placeholder' => 'Oui ou Non',
                ],
            ])
            ->add('STA_Attestation', TextType::class, [
                'label' => 'Attestation',
                'attr' => [
                    'placeholder' => 'Oui ou Non',
                ],
            ])
            ->add('STA_Jury', TextType::class, [
                'label' => 'Jury',
                'attr' => [
                    'placeholder' => 'Oui ou Non',
                ],
            ])
            ->add('STA_Commentaire')
            ->add('STA_DateRetenu', DateType::class, [
                'label' => 'Date retenu',
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
            ])
            ->add('ETU_ID', EntityType::class, [
                'class' => Etudiant::class,
                'label' => 'Étudiant*',
                'choice_label' => function (Etudiant $etudiant) {
                return $etudiant->getETUNom() . ' ' . $etudiant->getETUPrenom();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->orderBy('v.ETU_Nom', 'ASC');
                },
            ])
            ->add('ENT_ID', EntityType::class, [
                'class' => Entreprise::class,
                'label' => 'Entreprise*',
                'choice_label' => 'ENT_Nom',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->orderBy('v.ENT_Nom', 'ASC');
                },
            ])
            ->add('EnseignantVisite', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Enseignant de visite',
                'choice_label' => function (Utilisateur $utilisateur) {
                return $utilisateur->getNom() . ' ' . $utilisateur->getPrenom();
                },
                'query_builder' => function (EntityRepository $enseignant) {
                    return $enseignant->createQueryBuilder('u')
                        ->join('u.role', 'r')
                        ->orderBy('u.nom', 'ASC') 
                        ->where('r.libelle = :role') 
                        ->setParameter('role', 'Enseignant');
                },
            ])
            ->add('EnseignantSuivi', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Enseignant de suivi',
                'choice_label' => function (Utilisateur $utilisateur) {
                return $utilisateur->getNom() . ' ' . $utilisateur->getPrenom();
                },
                'query_builder' => function (EntityRepository $enseignant) {
                    return $enseignant->createQueryBuilder('u')
                        ->join('u.role', 'r') 
                        ->orderBy('u.nom', 'ASC') 
                        ->where('r.libelle = :role') 
                        ->setParameter('role', 'Enseignant');
                },
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
