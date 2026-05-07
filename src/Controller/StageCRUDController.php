<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Repository\EtudiantRepository;
use App\Entity\Historique;
use DateTime;
use App\Entity\Utilisateur;
use App\Entity\Etudiant;
use App\Entity\Entreprise;
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
    public function index(StageRepository $stageRepository, RequestStack $requestStack, EtudiantRepository $etudiantRepository, Request $request): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        $sort = $request->query->get('sort', 'ETU_Nom');
        $order = $request->query->get('order', 'asc');
        
        

        if ($userSession['role'] == 1) {
            // L'ADMIN : voit la liste globale avec les noms des profs
            $stages = $stageRepository->findAll();
            
                // on va chercher l'intégralité des stages via le repository
            return $this->render('stage_crud/index.html.twig', [
                'stages' => $stageRepository->findAllSorted($sort, $order),
                'etudiants' => $etudiantRepository->findAll(),
                'role' => $userSession['role'] ?? 0,
                'sort' => $sort,
                'order' => $order,
                ]);
        } else {
            // LE PROF : voit ses deux listes personnelles
            $mesSuivis = $stageRepository->findStagesByEnseignantSuivi($userSession['id']);
            $mesVisites = $stageRepository->findStagesByEnseignantVisite($userSession['id']);

            return $this->render('stage_crud/index.html.twig', [
                'mesSuivis' => $mesSuivis,
                'mesVisites' => $mesVisites,
                'role' => $userSession['role'],
                'stages' => $stageRepository->findAllSorted($sort, $order),
                'etudiants' => $etudiantRepository->findAll(),
                'sort' => $sort,
                'order' => $order,
            ]);
        }
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

            // Partie pour les logs
            $data = $form->getData();

            foreach ($form->all() as $fieldName => $field) {
                $value = $field->getData();

                if (is_object($value)) {
                    if(get_class($value) == 'App\Entity\Etudiant'){
                        $valeurAEnregistrer = (string)$value->getETUNom() . " " . (string)$value->getETUPrenom();
                    }
                    if(get_class($value) == 'App\Entity\Entreprise'){
                        $valeurAEnregistrer = $value->getENTNom();
                    }
                    if(get_class($value) == 'App\Entity\Utilisateur'){
                        $valeurAEnregistrer = (string)$value->getNom() . " " . (string)$value->getPrenom();
                    } 
                    if ($value instanceof \DateTimeInterface) {
                        $valeurAEnregistrer = (string)$value->format('d/m/Y');
                    }
                    
                } else {
                    $valeurAEnregistrer = (string)$value;
                }

                $historique = new Historique();
                $historique->setHISDate(new DateTime());
                $historique->setHISNouvelleValeur($valeurAEnregistrer);
                $historique->setHISAncienneValeur('');

                $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
                $historique->setUTIID($user);

                $entityManager->persist($historique);
            }

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
            
            $user = $entityManager->getRepository(Utilisateur::class)->find($userSession['id']);
            $dateLog = new DateTime();

            $etudiant = $entityManager->getRepository(Etudiant::class)->find($stage->getETUID());
            $entreprise = $entityManager->getRepository(Entreprise::class)->find($stage->getENTID());
            $enseignantVisite = $entityManager->getRepository(Utilisateur::class)->find($stage->getEnseignantVisite());
            $enseignantSuivi = $entityManager->getRepository(Utilisateur::class)->find($stage->getEnseignantSuivi());

            $anciennesValeurs = [
                (string)$stage->getSTADateDebut(),
                (string)$stage->getSTADateFin(),
                (string)$stage->getSTARemarque(),
                (string)$stage->getSTARemerciement(),
                (string)$stage->getSTABilan(),
                (string)$stage->getSTAAttestation(),
                (string)$stage->getSTAJury(),
                (string)$stage->getSTACommentaire(),
                (string)$stage->getSTADateRetenu(),
                (string)$etudiant->getETUNom() . " " . (string)$etudiant->getETUPrenom(),
                (string)$entreprise->getENTNom(),
                (string)$enseignantSuivi->getNom() . " " . (string)$enseignantSuivi->getPrenom(),
                (string)$enseignantVisite->getNom() . " " . (string)$enseignantVisite->getPrenom()
            ];

            foreach ($anciennesValeurs as $valeur) {
                $historique = new Historique();
                $historique->setHISDate($dateLog);
                $historique->setUTIID($user);
                $historique->setHISNouvelleValeur('');
                $historique->setHISAncienneValeur($valeur);

                $entityManager->persist($historique);
            }

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