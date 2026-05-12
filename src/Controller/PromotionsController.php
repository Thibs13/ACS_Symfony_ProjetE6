<?php

namespace App\Controller;

use App\Entity\Promotions;
use App\Entity\Historique;
use App\Form\PromotionsType;
use App\Repository\PromotionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use DateTime;
use App\Entity\Utilisateur;

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

            //partie log
            $data = $form->getData();
            $idSource = $promotion->getId();
            $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);


            foreach ($form->all() as $fieldName => $field) {
                $value = $field->getData();
                $valeurAEnregistrer = '';
            
                if (is_object($value)) {
                    if(get_class($value) == 'App\Entity\Promotions') {
                        $valeurAEnregistrer = $value->getProLibelle();
                    }
                    if(get_class($value) == 'DateTime') {
                        $valeurAEnregistrer = $value->format('Y-m-d H:i:s');
                    }
                }    
                else {
                    $valeurAEnregistrer = (string) $value;
                }
                

                $historique = new Historique();
                $historique->setHISDate(new DateTime());
                $historique->setHISNouvelleValeur($valeurAEnregistrer);
                $historique->setHISAncienneValeur('');
                $historique->setNomTable('Promotions');
                $historique->setIdSource($idSource);

                $historique->setUTIID($user);

                $entityManager->persist($historique);
            }

            $entityManager->flush();

            //fin partie log
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

        // 1. ON CAPTURE L'HISTORIQUE *AVANT* QUE LE FORMULAIRE MODIFIE L'OBJET
        $promotion = $entityManager->getRepository(Promotions::class)->find($promotion->getId());
        
        // Utilisation d'un tableau associatif pour éviter les erreurs d'ordre
        $anciennesValeurs = [
            'nom' => (string)$promotion->getProLibelle(),
            'session' => (string)$promotion->getProSession(),
            'dateDebut' => $promotion->getProDatedebut() ? $promotion->getProDatedebut()->format('Y-m-d H:i:s') : '',
            'dateFin' => $promotion->getProDatefin() ? $promotion->getProDatefin()->format('Y-m-d H:i:s') : '',
        ]; 

        // 2. ICI SYMFONY MET A JOUR L'OBJET AVEC LES DONNÉES DU POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        // 3. ON CAPTURE LES NOUVELLES VALEURS *APRÈS* LA MISE A JOUR
            $promotionNouvelle = $entityManager->getRepository(Promotions::class)->find($promotion->getId());

            $nouvellesValeurs = [
                'nom' => (string)$promotionNouvelle->getProLibelle(),
                'session' => (string)$promotionNouvelle->getProSession(),
                'dateDebut' => $promotionNouvelle->getProDatedebut() ? $promotionNouvelle->getProDatedebut()->format('Y-m-d H:i:s') : '',
                'dateFin' => $promotionNouvelle->getProDatefin() ? $promotionNouvelle->getProDatefin()->format('Y-m-d H:i:s') : '',
            ];

            $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
            $dateLog = new DateTime();

            // 4. ON COMPARE LES CLÉS IDENTIQUES
            foreach ($anciennesValeurs as $cle => $ancienneVal) {
                $nouvelleVal = $nouvellesValeurs[$cle];

                if ($ancienneVal !== $nouvelleVal) {
                    $historique = new Historique();
                    $historique->setHISDate($dateLog);
                    $historique->setUTIID($user);
                    $historique->setHISNouvelleValeur($nouvelleVal);
                    $historique->setHISAncienneValeur($ancienneVal);
                    $historique->setNomTable('promotions');
                    $historique->setIdSource($promotion->getId());

                    // (Optionnel) Tu pourrais même enregistrer le nom du champ modifié avec $cle !

                    $entityManager->persist($historique);
                }
            }

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
    public function delete(Request $request, Promotions $promotion, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        if ($this->isCsrfTokenValid('delete'.$promotion->getId(), $request->getPayload()->getString('_token'))) {
        
            // 1. On prépare les données communes à tous les logs
            $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
            $dateLog = new DateTime();

            // 2. On récupère les objets liés (Ville, Secteur) pour éviter les erreurs s'ils sont nulls
            $promotion = $entityManager->getRepository(Promotions::class)->find($promotion->getId());

            // 3. On liste toutes les anciennes valeurs que l'on veut sauvegarder
            $anciennesValeurs = [
                (string)$promotion->getProLibelle(),
                (string)$promotion->getProSession(),
                (string)$promotion->getProDatedebut()->format('Y-m-d H:i:s'),
                (string)$promotion->getProDatefin()->format('Y-m-d H:i:s')
            ];

            // 4. On boucle sur ces valeurs pour créer UN NOUVEAU log à chaque fois
            foreach ($anciennesValeurs as $valeur) {
                $historique = new Historique();
                $historique->setHISDate($dateLog);
                $historique->setUTIID($user);
                $historique->setHISNouvelleValeur('');
                $historique->setHISAncienneValeur($valeur);
                $historique->setNomTable('promotions');
                $historique->setIdSource($promotion->getId());

                $entityManager->persist($historique);
            }
        
            // 5. On supprime l'entreprise et on valide tout d'un coup
            $entityManager->remove($promotion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_promotions_read', [], Response::HTTP_SEE_OTHER);
    }
}
