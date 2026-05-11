<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Form\HistoriqueType;
use App\Repository\HistoriqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \Symfony\Component\HttpFoundation\RequestStack; 

#[Route('/historique')]
final class HistoriqueController extends AbstractController
{
    #[Route(name: 'app_historique_index', methods: ['GET'])]
    public function index(HistoriqueRepository $historiqueRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if ($userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('historique/index.html.twig', [
            'historiques' => $historiqueRepository->findAll(),
            'role' => $userSession['role'] ?? 0,
        ]);
    }
}
