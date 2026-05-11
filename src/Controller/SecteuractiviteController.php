<?php

namespace App\Controller;

use App\Entity\Secteuractivite;
use App\Form\SecteuractiviteType;
use App\Entity\Historique;
use App\Entity\Utilisateur;
use DateTime;
use App\Repository\SecteuractiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;

#[Route('/secteuractivite')]
final class SecteuractiviteController extends AbstractController
{
    #[Route(name: 'app_secteuractivite_read', methods: ['GET'])]
    public function index(SecteuractiviteRepository $secteuractiviteRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('secteuractivite/index.html.twig', [
            'secteuractivites' => $secteuractiviteRepository->findAll(),
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/new', name: 'app_secteuractivite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        $secteuractivite = new Secteuractivite();
        $form = $this->createForm(SecteuractiviteType::class, $secteuractivite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($secteuractivite);
            $entityManager->flush();

            $idSource = $secteuractivite->getId(); 

            // Partie pour les logs
            $data = $form->getData();

            foreach ($form->all() as $fieldName => $field) {
                $value = $field->getData();

                    $valeurAEnregistrer = (string)$value;

                    $historique = new Historique();
                    $historique->setHISDate(new DateTime());
                    $historique->setHISNouvelleValeur($valeurAEnregistrer);
                    $historique->setHISAncienneValeur('');
                    $historique->setNomTable('secteuractivite');
                    $historique->setIdSource($idSource);

                    $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
                    $historique->setUTIID($user);

                    $entityManager->persist($historique);
            }

            // Un seul flush global pour tous les logs
            $entityManager->flush();

            //Fin partie pour les logs

            return $this->redirectToRoute('app_secteuractivite_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('secteuractivite/new.html.twig', [
            'secteuractivite' => $secteuractivite,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/{id}', name: 'app_secteuractivite_show', methods: ['GET'])]
    public function show(Secteuractivite $secteuractivite, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('secteuractivite/show.html.twig', [
            'secteuractivite' => $secteuractivite,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_secteuractivite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Secteuractivite $secteuractivite, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        $form = $this->createForm(SecteuractiviteType::class, $secteuractivite);

        // Utilisation d'un tableau associatif pour éviter les erreurs d'ordre
        $anciennesValeurs = [
            'secteur' => (string)$secteuractivite->getSaLibelle(),
        ]; 

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nouvellesValeurs = [
                'secteur' => (string)$secteuractivite->getSaLibelle(),
            ];

            $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
            $dateLog = new DateTime();

            foreach ($anciennesValeurs as $cle => $ancienneVal) {
                $nouvelleVal = $nouvellesValeurs[$cle];

                if ($ancienneVal !== $nouvelleVal) {
                    $historique = new Historique();
                    $historique->setHISDate($dateLog);
                    $historique->setUTIID($user);
                    $historique->setHISNouvelleValeur($nouvelleVal);
                    $historique->setHISAncienneValeur($ancienneVal);
                    $historique->setNomTable('secteuractivite');
                    $historique->setIdSource($secteuractivite->getId());
                    
                    // (Optionnel) Tu pourrais même enregistrer le nom du champ modifié avec $cle !

                    $entityManager->persist($historique);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_secteuractivite_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('secteuractivite/edit.html.twig', [
            'secteuractivite' => $secteuractivite,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/{id}', name: 'app_secteuractivite_delete', methods: ['POST'])]
    public function delete(Request $request, Secteuractivite $secteuractivite, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        if ($this->isCsrfTokenValid('delete'.$secteuractivite->getId(), $request->getPayload()->getString('_token'))) {
            $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
            $dateLog = new DateTime();

            $anciennesValeurs = [
                (string)$secteuractivite->getSaLibelle(),
            ];

            foreach ($anciennesValeurs as $valeur) {
                $historique = new Historique();
                $historique->setHISDate($dateLog);
                $historique->setUTIID($user);
                $historique->setHISNouvelleValeur('');
                $historique->setHISAncienneValeur($valeur);
                $historique->setNomTable('secteuractivite');
                $historique->setIdSource($secteuractivite->getId());

                $entityManager->persist($historique);
            }

            $entityManager->remove($secteuractivite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_secteuractivite_read', [], Response::HTTP_SEE_OTHER);
    }
}
