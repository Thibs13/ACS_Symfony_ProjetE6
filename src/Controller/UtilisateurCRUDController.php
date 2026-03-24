<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/utilisateurs')]
final class UtilisateurCRUDController extends AbstractController
{
    // affiche la liste complète des utilisateurs enregistrés en base de données
    #[Route(name: 'app_utilisateur_read', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        // on récupère tous les utilisateurs pour les envoyer à la vue
        return $this->render('utilisateur_crud/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    // permet de créer un tout nouvel utilisateur
    #[Route('/new', name: 'app_utilisateur_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // on prépare un nouvel objet utilisateur et son formulaire
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        // si on valide le formulaire et qu'il n'y a pas d'erreurs
        if ($form->isSubmitted() && $form->isValid()) {
            // on demande à l'outil de gestion de préparer l'enregistrement
            $entityManager->persist($utilisateur);
            $entityManager->flush(); // on valide l'écriture en base de données

            // une fois terminé, on retourne à la liste globale
            return $this->redirectToRoute('app_utilisateur_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur_crud/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    // affiche les informations détaillées d'un utilisateur précis
    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        // symfony retrouve l'utilisateur tout seul grâce à l'identifiant dans l'adresse
        return $this->render('utilisateur_crud/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    // permet de modifier les informations d'un utilisateur existant
    #[Route('/{id}/edit', name: 'app_utilisateur_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        // on charge le formulaire avec les données actuelles de l'utilisateur
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        // si on confirme les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            // on met à jour la base de données
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur_crud/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    // supprime un utilisateur de la base de données
    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        // on vérifie que la demande de suppression est sécurisée par un jeton (token)
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->getPayload()->getString('_token'))) {
            // on supprime l'utilisateur et on valide le changement
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_read', [], Response::HTTP_SEE_OTHER);
    }
}