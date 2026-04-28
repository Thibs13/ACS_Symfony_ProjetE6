<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Route('/etudiants')]
final class EtudiantCRUDController extends AbstractController
{
    // affiche la liste de tous les étudiants enregistrés
    #[Route(name: 'app_etudiant_read', methods: ['GET'])]
    public function index(EtudiantRepository $etudiantRepository, FiliereRepository $filiereRepository, RequestStack $requestStack, Request $request): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        $sort = $request->query->get('sort', 'ETU_Nom');
        $order = $request->query->get('order', 'asc');
        $nombreEtudiants = $etudiantRepository->compteEtudiant();

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // on demande au repository de nous donner absolument tous les étudiants et les filières 
        return $this->render('etudiant_crud/index.html.twig', [
            'etudiants' => $etudiantRepository->findAllSorted($sort, $order),
            'filieres' => $filiereRepository->findAll(),
            'totalEtudiants' => $nombreEtudiants,
            'sort' => $sort,
            'order' => $order,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    #[Route('/export/excel', name: 'app_etudiant_export_excel', methods: ['GET'])]
    public function exportExcel(EtudiantRepository $etudiantRepository, RequestStack $requestStack, Request $request): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        $sort = $request->query->get('sort', 'ETU_Nom');
        $order = $request->query->get('order', 'asc');
        $etudiants = $etudiantRepository->findAllSorted($sort, $order);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Etudiants');
        $sheet->setCellValue('A1', 'Trigramme');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prenom');
        $sheet->setCellValue('D1', 'Classe');
        $sheet->setCellValue('E1', 'Session');

        $row = 2;
        foreach ($etudiants as $etudiant) {
            $prenom = $etudiant->getETUPrenom() ?? '';
            $nom = $etudiant->getETUNom() ?? '';
            $promo = $etudiant->getPromo();

            $sheet->setCellValue('A' . $row, strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 2)));
            $sheet->setCellValue('B' . $row, $nom);
            $sheet->setCellValue('C' . $row, $prenom);
            $sheet->setCellValue('D' . $row, $promo?->getProLibelle() ?? '');
            $sheet->setCellValue('E' . $row, $promo?->getProSession() ?? '');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $tempFile = tempnam(sys_get_temp_dir(), 'etudiants_export_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $response = new BinaryFileResponse($tempFile);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export_etudiants.xlsx');
        $response->deleteFileAfterSend(true);

        return $response;
    }

    // gère la création d'une nouvelle fiche étudiant
    #[Route('/new', name: 'app_etudiant_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

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
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // affiche les détails d'un étudiant spécifique (via son id dans l'URL)
    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');
    
        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // Symfony récupère automatiquement l'étudiant correspondant grâce à l'id
        return $this->render('etudiant_crud/show.html.twig', [
            'etudiant' => $etudiant,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // gère la modification d'une fiche étudiant existante
    #[Route('/{id}/edit', name: 'app_etudiant_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');
        
        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

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
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // gère la suppression d'une fiche étudiant
    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');
        
        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // on vérifie que la requête contient un token de sécurité valide pour éviter les attaques CSRF
        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            // si le token est valide, on dit à l'outil de gestion de base de données de supprimer l'étudiant
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        // une fois fini, on repart sur la liste globale
        return $this->redirectToRoute('app_etudiant_read', [], Response::HTTP_SEE_OTHER);
    }
}
