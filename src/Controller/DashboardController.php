<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\EtudiantRepository;
use App\Repository\StageRepository;
use App\Repository\EntrepriseRepository;


class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(RequestStack $requestStack, EtudiantRepository $etudiantRepository, StageRepository $stageRepository, EntrepriseRepository $entrepriseRepository): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // on va chercher les statistiques globales pour les afficher dans les compteurs du tableau de bord (Repository)
        $nombreEtudiants = $etudiantRepository->compteEtudiant();
        $nombreStages = $stageRepository->compteStage();
        $nombreEntreprises = $entrepriseRepository->compteEntreprise();
        
        // on récupère seulement les trois derniers stages pour l'affichage du flux récent (Repository)
        $derniersStages = $stageRepository->findThreeLatest();

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        // on transmet toutes les données récupérées à la vue pour construire la page
        return $this->render('Dashboard/index.html.twig', [
            'user' => $userSession,
            'totalEtudiants' => $nombreEtudiants, 
            'totalStages' => $nombreStages,  
            'totalEntreprises' => $nombreEntreprises,
            'stages' => $derniersStages,
        ]);
    }
}