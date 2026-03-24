<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\InscriptionType;
use App\Entity\Utilisateur;
use App\Entity\Role; 
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // on prépare le formulaire d'inscription pour l'afficher aux nouveaux utilisateurs
        $form = $this->createForm(InscriptionType::class);
        $form->handleRequest($request);

        $erreur = null;
        $succes = null;

        // quand l'utilisateur clique sur le bouton de validation
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère toutes les informations saisies dans les champs
            $data = $form->getData();
            
            $login  = $data['login'] ?? null;      
            $mdp    = $data['MotDePasse'] ?? null; 
            $nom    = $data['Nom'] ?? null;        
            $prenom = $data['Prenom'] ?? null;     

            // première vérification : est-ce que tous les champs sont bien remplis ?
            if (empty($login) || empty($mdp) || empty($nom) || empty($prenom)) {
                $erreur = 'Veuillez remplir tous les champs.';
            } 
            // on vérifie aussi que le login ne contient pas d'espace (important pour la connexion plus tard)
            elseif (str_contains($login, ' ')) {
                $erreur = 'Le login ne doit pas contenir d\'espaces.';
            } else {
                // on va vérifier en base de données si ce login n'est pas déjà pris
                $repo = $entityManager->getRepository(Utilisateur::class);
                $existingUser = $repo->findOneBy(['login' => $login]);

                if ($existingUser) {
                    $erreur = 'Un utilisateur avec ce login existe déjà.';
                } else {
                    // tout est bon ! on commence par sécuriser le mot de passe (on le hache)
                    $passwordHasher = new NativePasswordHasher();
                    $hashedPassword = $passwordHasher->hash($mdp);

                    // on crée le nouvel utilisateur avec les informations validées
                    $compte = new Utilisateur();
                    $compte->setLogin($login);        
                    $compte->setPassword($hashedPassword); 
                    $compte->setNom($nom);            
                    $compte->setPrenom($prenom);

                    // par défaut, on lui donne le rôle "Enseignant" en allant le chercher dans la table des rôles
                    $roleEntity = $entityManager->getRepository(Role::class)->findOneBy(['libelle' => 'Enseignant']);
                    if ($roleEntity) {
                        $compte->setRole($roleEntity); 
                    }

                    // on enregistre officiellement le nouveau compte dans la base de données
                    $entityManager->persist($compte);
                    $entityManager->flush();

                    // on prépare un petit message de réussite et on renvoie l'utilisateur vers la page de connexion
                    $this->addFlash('success', 'Inscription réussie.');
                    return $this->redirectToRoute('app_accueil'); 
                }
            }
        }

        // si on n'a pas encore validé ou s'il y a une erreur, on affiche la page d'inscription
        return $this->render('inscription/index.html.twig', [
            'form' => $form->createView(),
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }
}