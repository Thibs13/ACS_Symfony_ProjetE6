<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AccueilType;
use App\Entity\Utilisateur; 
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(Request $request, UtilisateurRepository $compteRepository, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // 1. Vérification de la session
        $userSession = $session->get('user');
        if ($userSession) {
            return $this->redirectToRoute('app_inscription');
        }

        $form = $this->createForm(AccueilType::class);
        $form->handleRequest($request);

        $erreur = null;
        $succes = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $login = $data['Login'] ?? null;
            $mdp = $data['MotDePasse'] ?? null;

            if (empty($login) || empty($mdp)) {
                $erreur = 'Veuillez remplir tous les champs.';
            } else {
                // 2. Recherche selon ton MCD : colonne UTI_Login
                // On suppose que dans ton entité la propriété est $login (mappée sur UTI_Login)
                $compte = $compteRepository->findOneBy(['login' => $login]);

                if ($compte) {
                    $passwordHasher = new NativePasswordHasher();
                    
                    // 3. Récupération du mot de passe (mappé sur UTI_Password)
                    $hashedPassword = $compte->getPassword(); 

                    if ($passwordHasher->verify($hashedPassword, $mdp)) {
                        // 4. Stockage en session simplifié
                        $session->set('user', [
                            'id' => $compte->getId(),
                            'login' => $compte->getLogin(),
                            'nom' => $compte->getNom(),
                        ]);

                        return $this->redirectToRoute('app_dashboard');
                    } else {
                        $erreur = 'Mot de passe incorrect.';
                    }
                } else {
                    $erreur = 'Identifiants incorrects.';
                }
            }
        }

        return $this->render('accueil/index.html.twig', [
            'form' => $form->createView(),
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }
}