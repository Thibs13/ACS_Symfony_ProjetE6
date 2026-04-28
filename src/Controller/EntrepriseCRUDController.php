<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\VilleRepository;
use \Symfony\Component\HttpFoundation\RequestStack;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/entreprise')]
final class EntrepriseCRUDController extends AbstractController
{
    // affiche la liste de toutes les entreprises enregistrées
    #[Route(name: 'app_entreprise_read', methods: ['GET'])]
    public function index(EntrepriseRepository $entrepriseRepository, VilleRepository $villeRepository, RequestStack $requestStack, Request $request): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        $sort = $request->query->get('sort', 'ENT_Nom');
        $order = $request->query->get('order', 'asc');
        // on demande au repository de nous donner absolument toutes les entreprises et les villes 
        return $this->render('entreprise_crud/index.html.twig', [
            'entreprises' => $entrepriseRepository->findAllSorted($sort, $order),
            'villes' => $villeRepository->findAll(),
            'role' => $userSession['role'] ?? 0,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    // gère la création d'une nouvelle fiche entreprise
    #[Route('/new', name: 'app_entreprise_create', methods: ['GET', 'POST'])]
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
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        // si le formulaire est envoyé et que les données sont correctes
        if ($form->isSubmitted() && $form->isValid()) {
            // on dit à l'outil de gestion de base de données de "préparer" puis "d'enregistrer" la nouvelle entreprise
            $entityManager->persist($entreprise);
            $entityManager->flush();

            // une fois fini, on repart sur la liste globale
            return $this->redirectToRoute('app_entreprise_read', [], Response::HTTP_SEE_OTHER);
        }

        // sinon, on affiche juste la page avec le formulaire à remplir
        return $this->render('entreprise_crud/new.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }

    // affiche les détails d'une entreprise spécifique (via son id dans l'URL)
    #[Route('/{id}', name: 'app_entreprise_show', methods: ['GET'])]
    public function show(Entreprise $entreprise, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession) {
            return $this->redirectToRoute('app_accueil');
        }

        // Symfony récupère automatiquement l'entreprise correspondante grâce à l'id
        return $this->render('entreprise_crud/show.html.twig', [
            'entreprise' => $entreprise,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // permet de modifier les informations d'une entreprise existante
    #[Route('/{id}/edit', name: 'app_entreprise_update', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // on crée le formulaire pré-rempli avec les données actuelles de l'entreprise
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        // si on valide les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            // on met à jour la base de données (pas besoin de persist ici, l'objet existe déjà)
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise_read', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('entreprise_crud/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
            'role' => $userSession['role'] ?? 0,
        ]);
    }

    // supprime définitivement une entreprise
    #[Route('/{id}', name: 'app_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        // par sécurité, on vérifie que le jeton (token) de suppression est valide
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->getPayload()->getString('_token'))) {
            // on retire l'entreprise de la base de données et on valide le changement
            $entityManager->remove($entreprise);
            $entityManager->flush();
        }

        // on revient à la liste après la suppression
        return $this->redirectToRoute('app_entreprise_read', [
            'role' => $userSession['role'] ?? 0,
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export/excel', name: 'app_entreprise_export_excel', methods: ['GET'])]
    public function exportExcel(EntrepriseRepository $entrepriseRepository, RequestStack $requestStack, Request $request): Response
    {
        // on récupère la session en cours pour vérifier qui navigue sur le site
        $session = $requestStack->getSession();
        $userSession = $session->get('user');

        // si personne n'est connecté, on renvoie l'utilisateur vers la page de connexion
        if (!$userSession OR $userSession['role'] != 1) {
            return $this->redirectToRoute('app_accueil');
        }

        $sort = $request->query->get('sort', 'ENT_Nom');
        $order = $request->query->get('order', 'asc');
        $entreprises = $entrepriseRepository->findAllSorted($sort, $order);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Entreprises');
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Téléphone');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Ville');
        $sheet->setCellValue('E1', 'Adresse');
        $sheet->setCellValue('F1', 'Secteur d\'activité');

        $row = 2;
        foreach ($entreprises as $entreprise) {
            $nom = $entreprise->getENTNom() ?? '';
            $adresse = $entreprise->getENTAdresse() ?? '';
            $ville = $entreprise->getVILID()?->getVILNom() ?? '';
            $telephone = $entreprise->getENTTelephone() ?? '';
            $email = $entreprise->getENTEmail() ?? '';
            $secteurActivite = $entreprise->getSecteur()?->getSaLibelle() ?? '';

            $sheet->setCellValue('A' . $row, $nom);
            $sheet->setCellValue('B' . $row, $telephone);
            $sheet->setCellValue('C' . $row, $email);
            $sheet->setCellValue('D' . $row, $ville);
            $sheet->setCellValue('E' . $row, $adresse);
            $sheet->setCellValue('F' . $row, $secteurActivite);
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $tempFile = tempnam(sys_get_temp_dir(), 'entreprises_export_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $response = new BinaryFileResponse($tempFile);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export_entreprises.xlsx');
        $response->deleteFileAfterSend(true);

        return $response;
    }
}