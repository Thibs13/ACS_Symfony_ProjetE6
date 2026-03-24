<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'app_utilisateurs')]
    public function index(RequestStack $requestStack): Response
    {
        // on récupère les informations de la session actuelle pour savoir qui est connecté
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si on ne trouve aucune session (utilisateur non connecté), on le renvoie vers la page d'accueil
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        // on affiche la page de profil en envoyant les informations de l'utilisateur stockées en session
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $userSession,
        ]);
    }
}
