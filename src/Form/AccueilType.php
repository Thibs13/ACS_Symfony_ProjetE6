<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank; // Ajout pour la sécurité

class AccueilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le login (mappé sur UTI_Login en base)
            ->add('Login', TextType::class, [
                'label' => 'Identifiant',
                'required' => true, // On force le remplissage côté navigateur
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre login.']), // Sécurité côté serveur
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre identifiant',
                    'class' => 'form-control'
                ],
            ])

            // Champ pour le mot de passe (mappé sur UTI_Password)
            ->add('MotDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre mot de passe.']),
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre mot de passe',
                    'class' => 'form-control'
                ],
            ])

            // Bouton de soumission
            ->add('Save', SubmitType::class, [
                'label' => 'Se connecter',
                'attr' => [
                    'class' => 'btn-submit' // Utilise la classe CSS que tu as définie dans ton Twig
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // On laisse à null car c'est un formulaire de traitement de session
            'data_class' => null, 
        ]);
    }
}