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

        $nombreEtudiants = $etudiantRepository->compteEtudiant();
        $nombreStages = $stageRepository->compteStage();
        $nombreEntreprises = $entrepriseRepository->compteEntreprise();
        $derniersStages = $stageRepository->findThreeLatest();

        // Si l'utilisateur n'est pas en session redirection 
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('Dashboard/index.html.twig', [
            'user' => $userSession,
            'totalEtudiants' => $nombreEtudiants, 
            'totalStages' => $nombreStages,  
            'totalEntreprises' => $nombreEntreprises,
            'stages' => $derniersStages,
        ]);
    }
}