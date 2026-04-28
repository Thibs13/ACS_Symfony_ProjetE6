<?php

namespace App\Controller;

use App\Entity\Promotions;
use App\Form\PromotionsType;
use App\Repository\PromotionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;

#[Route('/promotions')]
final class PromotionsController extends AbstractController
{
    #[Route(name: 'app_promotions_read', methods: ['GET'])]
    public function index(PromotionsRepository $promotionsRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('promotions/index.html.twig', [
            'promotions' => $promotionsRepository->findAll(),
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/new', name: 'app_promotions_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        $promotion = new Promotions();
        $form = $this->createForm(PromotionsType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($promotion);
            $entityManager->flush();

            return $this->redirectToRoute('app_promotions_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('promotions/new.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/{id}', name: 'app_promotions_show', methods: ['GET'])]
    public function show(Promotions $promotion, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('promotions/show.html.twig', [
            'promotion' => $promotion,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_promotions_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Promotions $promotion, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        $form = $this->createForm(PromotionsType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_promotions_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('promotions/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/{id}', name: 'app_promotions_delete', methods: ['POST'])]
    public function delete(Request $request, Promotions $promotion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$promotion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($promotion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_promotions_read', [], Response::HTTP_SEE_OTHER);
    }
}
