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
    #[Route('', name: 'app_etudiant_read', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EtudiantRepository $etudiantRepository,
        FiliereRepository $filiereRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($request->isMethod('POST')) {
            $nom = trim((string) $request->request->get('nom', ''));
            $prenom = trim((string) $request->request->get('prenom', ''));
            $promotionInput = $request->request->get('promotion');
            $filiereId = $request->request->get('filiere');
            $filiere = is_scalar($filiereId) ? $filiereRepository->find((int) $filiereId) : null;

            if (
                $this->isCsrfTokenValid('create_etudiant', (string) $request->request->get('_token'))
                && $nom !== ''
                && $prenom !== ''
                && is_numeric((string) $promotionInput)
                && $filiere !== null
            ) {
                $etudiant = new Etudiant();
                $etudiant->setETUNom($nom);
                $etudiant->setETUPrenom($prenom);
                $etudiant->setETUPromotion((int) $promotionInput);
                $etudiant->setFILID($filiere);

                $entityManager->persist($etudiant);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_etudiant_read');
        }

        return $this->render('etudiant_crud/index.html.twig', [
            'etudiants' => $etudiantRepository->findAll(),
            'filieres' => $filiereRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etudiant_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant_crud/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('etudiant_crud/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_update', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Etudiant $etudiant,
        EntityManagerInterface $entityManager,
        FiliereRepository $filiereRepository
    ): Response
    {
        if ($request->isMethod('POST') && $request->request->has('nom')) {
            $nom = trim((string) $request->request->get('nom', ''));
            $prenom = trim((string) $request->request->get('prenom', ''));
            $promotionInput = $request->request->get('promotion');
            $filiereId = $request->request->get('filiere');
            $filiere = is_scalar($filiereId) ? $filiereRepository->find((int) $filiereId) : null;

            if (
                $this->isCsrfTokenValid('edit_etudiant_'.$etudiant->getId(), (string) $request->request->get('_token'))
                && $nom !== ''
                && $prenom !== ''
                && is_numeric((string) $promotionInput)
                && $filiere !== null
            ) {
                $etudiant->setETUNom($nom);
                $etudiant->setETUPrenom($prenom);
                $etudiant->setETUPromotion((int) $promotionInput);
                $etudiant->setFILID($filiere);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
        }

        // Cas 2: formulaire Symfony classique de la page /edit
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant_crud/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etudiant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
    }
}
