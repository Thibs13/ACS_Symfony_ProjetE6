<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etudiants')]
final class EtudiantCRUDController extends AbstractController
{
    // affiche la liste de tous les étudiants enregistrés
    #[Route(name: 'app_etudiant_read', methods: ['GET'])]
    public function index(EtudiantRepository $etudiantRepository, FiliereRepository $filiereRepository): Response
    {
        // on demande au repository de nous donner absolument tous les étudiants et les filières 
        return $this->render('etudiant_crud/index.html.twig', [
            'etudiants' => $etudiantRepository->findAll(),
            'filieres' => $filiereRepository->findAll(),
        ]);
    }

    // gère la création d'une nouvelle fiche étudiant
    #[Route('/new', name: 'app_etudiant_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // on prépare un objet vide et le formulaire qui va avec
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        // si le formulaire est envoyé et que les données sont correctes
        if ($form->isSubmitted() && $form->isValid()) {
            // on dit à l'outil de gestion de base de données de "préparer" puis "d'enregistrer" le nouvel étudiant
            $entityManager->persist($etudiant);
            $entityManager->flush();

            // une fois fini, on repart sur la liste globale
            return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
        }

        // sinon, on affiche juste la page avec le formulaire à remplir
        return $this->render('etudiant_crud/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    // affiche les détails d'un étudiant spécifique (via son id dans l'URL)
    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant): Response
    {
        // Symfony récupère automatiquement l'étudiant correspondant grâce à l'id
        return $this->render('etudiant_crud/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    // gère la modification d'une fiche étudiant existante
    #[Route('/{id}/edit', name: 'app_etudiant_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        // on prépare le formulaire pré-rempli avec les données de l'étudiant à modifier
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        // si le formulaire est envoyé et que les données sont correctes
        if ($form->isSubmitted() && $form->isValid()) {
            // on dit à l'outil de gestion de base de données de "préparer" puis "d'enregistrer" les modifications de l'étudiant
            $entityManager->persist($etudiant);
            $entityManager->flush();

            // une fois fini, on repart sur la liste globale
            return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
        }

        // sinon, on affiche juste la page avec le formulaire à remplir
        return $this->render('etudiant_crud/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    // gère la suppression d'une fiche étudiant
    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        // on vérifie que la requête contient un token de sécurité valide pour éviter les attaques CSRF
        if ($this->isCsrfTokenValid('delete_etudiant_' . $etudiant->getId(), $request->request->get('_token'))) {
            // si le token est valide, on dit à l'outil de gestion de base de données de supprimer l'étudiant
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        // une fois fini, on repart sur la liste globale
        return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
    }
}
