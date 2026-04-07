<?php

namespace App\Controller;

use App\Entity\Secteuractivite;
use App\Form\SecteuractiviteType;
use App\Repository\SecteuractiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/secteuractivite')]
final class SecteuractiviteController extends AbstractController
{
    #[Route(name: 'app_secteuractivite_read', methods: ['GET'])]
    public function index(SecteuractiviteRepository $secteuractiviteRepository): Response
    {
        return $this->render('secteuractivite/index.html.twig', [
            'secteuractivites' => $secteuractiviteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_secteuractivite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $secteuractivite = new Secteuractivite();
        $form = $this->createForm(SecteuractiviteType::class, $secteuractivite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($secteuractivite);
            $entityManager->flush();

            return $this->redirectToRoute('app_secteuractivite_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('secteuractivite/new.html.twig', [
            'secteuractivite' => $secteuractivite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_secteuractivite_show', methods: ['GET'])]
    public function show(Secteuractivite $secteuractivite): Response
    {
        return $this->render('secteuractivite/show.html.twig', [
            'secteuractivite' => $secteuractivite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_secteuractivite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Secteuractivite $secteuractivite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SecteuractiviteType::class, $secteuractivite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_secteuractivite_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('secteuractivite/edit.html.twig', [
            'secteuractivite' => $secteuractivite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_secteuractivite_delete', methods: ['POST'])]
    public function delete(Request $request, Secteuractivite $secteuractivite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$secteuractivite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($secteuractivite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_secteuractivite_read', [], Response::HTTP_SEE_OTHER);
    }
}
