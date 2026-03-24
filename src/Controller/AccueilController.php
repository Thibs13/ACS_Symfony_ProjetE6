<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AccueilType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(Request $request, UtilisateurRepository $compteRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Si l'utilisateur est déjà connecté, on l'envoie direct au dashboard
        $userSession = $session->get('user');
        if ($userSession) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Préparation du formulaire de connexion
        $form = $this->createForm(AccueilType::class);
        $form->handleRequest($request);

        $erreur = null;
        $succes = null;

        // Analyse du formulaire (clique sur "connexion")
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); // On récupère les infos saisies
            
            $login = $data['login'] ?? null;     
            $mdp = $data['password'] ?? null;   

            // Vérification si les champs sont vides
            if (empty($login) || empty($mdp)) {
                $erreur = 'Veuillez remplir tous les champs.';
            } else {
                // Recherche de l'utilisateur en base de données par son login
                $compte = $compteRepository->findOneBy(['login' => $login]);

                if ($compte) {
                    // Vérification du mot de passe
                    if ($passwordHasher->isPasswordValid($compte, $mdp)) {
                        
                        // Connexion réussie : On enregistre les infos importantes dans la Session
                        $session->set('user', [
                            'id' => $compte->getId(),
                            'login' => $compte->getLogin(),
                            'nom' => $compte->getNom(),
                        ]);

                        // On redirige vers la page d'accueil du site
                        return $this->redirectToRoute('app_dashboard');
                    } else {
                        $erreur = 'Mot de passe incorrect.';
                    }
                } else {
                    $erreur = 'Identifiants incorrects.';
                }
            }
        }

        // Affichage de la page de connexion avec les éventuels messages d'erreur
        return $this->render('connexion/index.html.twig', [
            'form' => $form->createView(),
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }
}