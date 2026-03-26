<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Form\StageType;
use App\Repository\StageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \Symfony\Component\HttpFoundation\RequestStack; 


#[Route('/stage')]
final class StageCRUDController extends AbstractController
{
    // affiche la liste de tous les stages enregistrés dans le système
    #[Route(name: 'app_stage_read', methods: ['GET'])]
    public function index(StageRepository $stageRepository, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        // on va chercher l'intégralité des stages via le repository
        return $this->render('stage_crud/index.html.twig', [
            'stages' => $stageRepository->findAll(),
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // permet de créer un nouveau stage via un formulaire
    #[Route('/new', name: 'app_stage_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {

        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // on initialise un nouvel objet Stage et on génère le formulaire correspondant
        $stage = new Stage();
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        // si l'utilisateur a cliqué sur valider et que les infos sont correctes
        if ($form->isSubmitted() && $form->isValid()) {
            // on demande à Doctrine de préparer l'ajout en base de données
            $entityManager->persist($stage);
            $entityManager->flush(); // on valide définitivement l'écriture en base

            // une fois enregistré, on redirige vers la liste des stages
            return $this->redirectToRoute('app_stage_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stage_crud/new.html.twig', [
            'stage' => $stage,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // affiche le détail complet d'un stage (entreprise, étudiant, dates, etc.)
    #[Route('/{id}', name: 'app_stage_show', methods: ['GET'])]
    public function show(Stage $stage, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        // Symfony trouve tout seul le bon stage grâce à l'ID présent dans l'adresse
        return $this->render('stage_crud/show.html.twig', [
            'stage' => $stage,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // permet de modifier les informations d'un stage déjà existant
    #[Route('/{id}/edit', name: 'app_stage_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stage $stage, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // on charge le formulaire avec les données actuelles du stage
        $form = $this->createForm(StageType::class, $stage);
        $form->handleRequest($request);

        // si on valide les changements
        if ($form->isSubmitted() && $form->isValid()) {
            // on applique les modifications directement en base de données
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stage_crud/edit.html.twig', [
            'stage' => $stage,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // gère la suppression d'un stage
    #[Route('/{id}', name: 'app_stage_delete', methods: ['POST'])]
    public function delete(Request $request, Stage $stage, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // petite vérification de sécurité pour être sûr que la demande de suppression est légitime
        if ($this->isCsrfTokenValid('delete'.$stage->getId(), $request->getPayload()->getString('_token'))) {
            // on donne l'ordre de supprimer le stage et on valide
            $entityManager->remove($stage);
            $entityManager->flush();
        }

        // on revient à la liste principale une fois l'action terminée
        return $this->redirectToRoute('app_stage_read', [
            'role' => $userSession['role'] ?? 0,
        ], Response::HTTP_SEE_OTHER);
    }
}