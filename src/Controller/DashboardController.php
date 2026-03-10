<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // Si l'utilisateur n'est pas en session redirection 
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('dashboard/index.html.twig', [
            'user' => $userSession,
        ]);
    }
}