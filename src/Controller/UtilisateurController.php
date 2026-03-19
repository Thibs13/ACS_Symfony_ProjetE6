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
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }
        // On retourne la vue en lui passant le tableau d'utilisateurs
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $userSession,
        ]);
    }

}
