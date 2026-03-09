<?php
    namespace App\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use App\Repository\UtilisateurRepository;

    class InscriptionType extends AbstractType
    {
        private UtilisateurRepository $UtilisateurRepository;

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add(
                    'Login',
                    TextType::class,
                    [
                        'required' => true,
                        'attr' => [
                            'placeholder' => 'Saisir un login',
                            'class' => 'form-control'
                        ],
                    ]
                )

                ->add('MotDePasse', PasswordType::class, [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Saisir un mot de passe',
                        'class' => 'form-control'
                    ],
                ])

                ->add('Nom', TextType::class, [

                    'required' => true,
                    'attr' => [

                        'placeholder' => 'Saisir un nom',
                        'class' => 'form-control'
                    ],
                ])

                ->add('Prenom', TextType::class, [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Saisir un prénom',
                        'class' => 'form-control'
                    ],
                ])

                ->add('Role', ChoiceType::class, [
                    'choices' => [
                        'Administrateur' => 'administrateur',
                        'Enseignant' => 'Enseignant',
                    ],
                    'data' => 'profs',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Saisir un rôle',
                        'class' => 'form-control'
                    ],
                ])

                ->add('Save', SubmitType::class, [
                    'label' => 'S\'inscrire',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ],
                ])
            ;
        }
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => null,
            ]);
        }
    }
?>