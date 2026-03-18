<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Filiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ETU_Nom', null, ['label' => 'Nom'])
            ->add('ETU_Prenom', null, ['label' => 'Prénom'])
            ->add('ETU_Promotion', null, ['label' => 'Promotion'])
            ->add('FIL_ID', EntityType::class, [
                'class' => Filiere::class,
                'label' => 'Filière',
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
