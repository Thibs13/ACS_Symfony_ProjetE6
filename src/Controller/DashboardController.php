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
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        $role = $userSession['role'] ?? 0;

        // Statistiques globales (communes aux deux rôles)
        $nombreEtudiants = $etudiantRepository->compteEtudiant();
        $nombreStages = $stageRepository->compteStage();
        $nombreEntreprises = $entrepriseRepository->compteEntreprise();

        // Initialisation des variables pour éviter les erreurs Twig
        $derniersStages = [];
        $mesVisites = [];
        $mesSuivis = [];

        if ($role == 1) {
            // Pour l'admin : les 3 derniers stages globaux
            $derniersStages = $stageRepository->findThreeLatest();
        } elseif ($role == 2) {
            // Pour le prof : on récupère ses propres suivis et visites
            // Note : on utilise l'ID de l'utilisateur stocké en session
            $userId = $userSession['id']; 
            $mesVisites = $stageRepository->findBy(['EnseignantVisite' => $userId]);
            $mesSuivis = $stageRepository->findBy(['EnseignantSuivi' => $userId]);
        }

        return $this->render('Dashboard/index.html.twig', [
            'user' => $userSession,
            'role' => $role,
            'totalEtudiants' => $nombreEtudiants, 
            'totalStages' => $nombreStages,  
            'totalEntreprises' => $nombreEntreprises,
            'stages' => $derniersStages,
            'mesVisites' => $mesVisites,
            'mesSuivis' => $mesSuivis,
        ]);
    }
}