<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\InscriptionType;
use App\Entity\Utilisateur;
use App\Entity\Role; // Correction de l'import (minuscule sur app)
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        /* On vérifie si l'utilisateur est déjà en session
        $userSession = $session->get('user');
        if ($userSession) {
            return $this->redirectToRoute('app_dashboard');
        }*/

        $form = $this->createForm(InscriptionType::class);
        $form->handleRequest($request);

        $erreur = null;
        $succes = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $login = $data['Login'] ?? null;
            $mdp = $data['MotDePasse'] ?? null;
            $nom = $data['Nom'] ?? null;
            $prenom = $data['Prenom'] ?? null;

            if (empty($login) || empty($mdp) || empty($nom) || empty($prenom)) {
                $erreur = 'Veuillez remplir tous les champs.';
            } else {
                $repo = $entityManager->getRepository(Utilisateur::class);
                $existingUser = $repo->findOneBy(['login' => $login]);

                if ($existingUser) {
                    $erreur = 'Un utilisateur avec ce login existe déjà.';
                } else {
                    $passwordHasher = new NativePasswordHasher();
                    $hashedPassword = $passwordHasher->hash($mdp);

                    $compte = new Utilisateur();
                    $compte->setLogin($login);        
                    $compte->setPassword($hashedPassword); 
                    $compte->setNom($nom);            
                    $compte->setPrenom($prenom);

                    $roleEntity = $entityManager->getRepository(Role::class)->findOneBy(['libelle' => 'Enseignant']);
                    if ($roleEntity) {
                        $compte->setRole($roleEntity); 
                    }

                    $entityManager->persist($compte);
                    $entityManager->flush();

                    $this->addFlash('success', 'Inscription réussie.');
                    return $this->redirectToRoute('app_accueil'); 
                }
            }
        }

        return $this->render('inscription/index.html.twig', [
            'form' => $form->createView(),
            'erreur' => $erreur,
            'succes' => $succes
        ]);
    }
}