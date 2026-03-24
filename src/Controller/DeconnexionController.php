<?php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use App\Form\AjoutType;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;

    final class DeconnexionController extends AbstractController
    {
        #[Route('/deco', name: 'app_deco')]
        public function index(SessionInterface $session): Response
        {
            $user = $session->get('user');
            if (!$user) {
                return $this->redirectToRoute('app_accueil'); // Redirige vers la page de connexion si non connecté
            }
            // Détruire la session pour déconnecter l'utilisateur
            $session->invalidate();
            $session->clear(); // Efface toutes les données de session
            return $this->redirectToRoute('app_accueil'); // Redirige vers la page d'accueil
        }
    }
?>