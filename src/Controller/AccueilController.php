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
        $userSession = $session->get('user');
        if ($userSession) {
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(AccueilType::class);
        $form->handleRequest($request);

        $erreur = null;
        $succes = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // IMPORTANT : Utilise les mêmes noms que dans ton AccueilType (minuscules)
            $login = $data['login'] ?? null;     // 'login' au lieu de 'Login'
            $mdp = $data['password'] ?? null;   // 'password' au lieu de 'MotDePasse'

            if (empty($login) || empty($mdp)) {
                $erreur = 'Veuillez remplir tous les champs.';
            } else {
                $compte = $compteRepository->findOneBy(['login' => $login]);

                if ($compte) {
                    // Utilisation directe du service injecté en paramètre
                    if ($passwordHasher->isPasswordValid($compte, $mdp)) {
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

        return $this->render('connexion/index.html.twig', [
            'form' => $form->createView(),
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }
}