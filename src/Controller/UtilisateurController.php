<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'app_utilisateurs')]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        // On récupère tous les utilisateurs depuis la base de données
        $utilisateurs = $utilisateurRepository->findAll();

        // On retourne la vue en lui passant le tableau d'utilisateurs
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
}