<?php

namespace App\Controller;

use App\Attribute\RequireModule;
use App\Entity\Contrat;
use App\Entity\LigneArticle;
use App\Entity\Projet;
use App\Enum\CodeModule;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use App\Repository\ProduitRepository;
use App\Repository\SocieteRepository;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contrats')]
#[RequireModule(CodeModule::CONTRATS)]
class ContratController extends AbstractController
{
    #[Route('', name: 'app_contrat_index', methods: ['GET'])]
    #[IsGranted('ROLE_CONTRATS_VOIR')]
    public function index(ContratRepository $repository): Response
    {
        return $this->render('contrat/index.html.twig', [
            'contrats' => $repository->findBy([], ['dateEmission' => 'DESC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTRATS_CREER')]
    public function new(Request $request, EntityManagerInterface $em, ProduitRepository $produitRepository): Response
    {
        // Un contrat est toujours rattaché à un projet : il en faut au moins un.
        if ($em->getRepository(Projet::class)->count([]) === 0) {
            $this->addFlash('warning', 'Créez d\'abord un projet : un devis/contrat doit toujours être rattaché à un projet.');

            return $this->redirectToRoute('app_projet_new');
        }

        // Un contrat doit contenir des articles du catalogue : il faut au moins un produit actif.
        if ($produitRepository->count(['actif' => true]) === 0) {
            $this->addFlash('warning', 'Ajoutez d\'abord au moins un article au catalogue avant de créer un devis/contrat.');

            return $this->redirectToRoute('app_produit_index');
        }

        $contrat = new Contrat();

        // Pré-sélection du projet si fourni en query (?projet=ID)
        $projetId = $request->query->getInt('projet');
        if ($projetId) {
            $projet = $em->getRepository(Projet::class)->find($projetId);
            if ($projet) {
                $contrat->setProjet($projet);
            }
        }

        // Toujours afficher au moins une ligne à remplir (ne dépend pas du JS).
        if ($contrat->getLignes()->isEmpty()) {
            $contrat->addLigne(new LigneArticle());
        }

        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contrat);
            $em->flush();
            $this->addFlash('success', 'Document créé.');

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/form.html.twig', ['form' => $form, 'titre' => 'Nouveau devis / contrat']);
    }

    #[Route('/{id}', name: 'app_contrat_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_CONTRATS_VOIR')]
    public function show(Contrat $contrat): Response
    {
        return $this->render('contrat/show.html.twig', ['contrat' => $contrat]);
    }

    #[Route('/{id}/pdf', name: 'app_contrat_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_CONTRATS_VOIR')]
    public function pdf(Contrat $contrat, PdfGenerator $pdf, SocieteRepository $societeRepository): Response
    {
        return $pdf->reponseDepuisTemplate('pdf/contrat.html.twig', [
            'contrat' => $contrat,
            'societe' => $societeRepository->getSociete(),
        ], ($contrat->getReference() ?: 'document') . '.pdf');
    }

    #[Route('/{id}/modifier', name: 'app_contrat_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_CONTRATS_MODIFIER')]
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Document modifié.');

            return $this->redirectToRoute('app_contrat_show', ['id' => $contrat->getId()]);
        }

        return $this->render('contrat/form.html.twig', ['form' => $form, 'titre' => 'Modifier le devis / contrat']);
    }

    #[Route('/{id}/supprimer', name: 'app_contrat_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_CONTRATS_SUPPRIMER')]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->request->get('_token'))) {
            $contrat->setSupprime(true);
            $em->flush();
            $this->addFlash('success', 'Document supprimé.');
        }

        return $this->redirectToRoute('app_contrat_index');
    }
}
