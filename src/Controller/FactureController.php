<?php

namespace App\Controller;

use App\Attribute\RequireModule;
use App\Entity\Contrat;
use App\Entity\Facture;
use App\Entity\LigneArticle;
use App\Entity\Projet;
use App\Enum\CodeModule;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Repository\ProduitRepository;
use App\Repository\SocieteRepository;
use App\Service\FactureNumeroGenerator;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/factures')]
#[RequireModule(CodeModule::FACTURATION)]
class FactureController extends AbstractController
{
    #[Route('', name: 'app_facture_index', methods: ['GET'])]
    #[IsGranted('ROLE_FACTURATION_VOIR')]
    public function index(FactureRepository $repository): Response
    {
        return $this->render('facture/index.html.twig', [
            'factures' => $repository->findBy([], ['dateEmission' => 'DESC', 'id' => 'DESC']),
        ]);
    }

    #[Route('/nouvelle', name: 'app_facture_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FACTURATION_CREER')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        ProduitRepository $produitRepository,
        FactureNumeroGenerator $numeroGenerator,
    ): Response {
        if ($em->getRepository(Projet::class)->count([]) === 0) {
            $this->addFlash('warning', 'Créez d\'abord un projet : une facture doit être rattachée à un projet.');

            return $this->redirectToRoute('app_projet_new');
        }
        if ($produitRepository->count(['actif' => true]) === 0) {
            $this->addFlash('warning', 'Ajoutez d\'abord au moins un article au catalogue.');

            return $this->redirectToRoute('app_produit_index');
        }

        $facture = new Facture();
        if ($facture->getLignes()->isEmpty()) {
            $facture->addLigne(new LigneArticle());
        }

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture->appliquerDelaiPaiement();
            $facture->setNumero($numeroGenerator->generer());
            $em->persist($facture);
            $em->flush();
            $this->addFlash('success', 'Facture créée (' . $facture->getNumero() . ').');

            return $this->redirectToRoute('app_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render('facture/form.html.twig', ['form' => $form, 'titre' => 'Nouvelle facture']);
    }

    #[Route('/depuis-contrat/{id}', name: 'app_facture_from_contrat', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_FACTURATION_CREER')]
    public function depuisContrat(
        Request $request,
        Contrat $contrat,
        EntityManagerInterface $em,
        FactureNumeroGenerator $numeroGenerator,
        FactureRepository $factureRepository,
    ): Response {
        if (!$this->isCsrfTokenValid('facturer' . $contrat->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $facture = (new Facture())
            ->setProjet($contrat->getProjet())
            ->setContact($contrat->getProjet()?->getContact())
            ->setContrat($contrat)
            ->setNotes($contrat->getNotes());

        // Snapshot du déjà-facturé (facturation à l'avancement) figé à la création.
        $dejaHt = 0.0;
        $dejaTva = 0.0;
        foreach ($factureRepository->duContrat($contrat) as $factureExistante) {
            $dejaHt += $factureExistante->getTotalHt();
            $dejaTva += $factureExistante->getTotalTva();
        }
        if ($dejaHt != 0.0 || $dejaTva != 0.0) {
            $facture->setMontantDejaFactureHt(number_format($dejaHt, 2, '.', ''));
            $facture->setMontantDejaFactureTva(number_format($dejaTva, 2, '.', ''));
        }

        foreach ($contrat->getLignes() as $ligneContrat) {
            $ligne = (new LigneArticle())
                ->setProduit($ligneContrat->getProduit())
                ->setQuantite($ligneContrat->getQuantite())
                ->setPrixUnitaireHt($ligneContrat->getPrixUnitaireHt())
                ->setTauxTva($ligneContrat->getTauxTva());
            $ligne->setDesignation($ligneContrat->getDesignation());
            $ligne->setLigneSource($ligneContrat);
            $facture->addLigne($ligne);
        }

        $facture->setNumero($numeroGenerator->generer());
        $em->persist($facture);
        $em->flush();
        $this->addFlash('success', 'Facture ' . $facture->getNumero() . ' créée depuis le contrat.');

        return $this->redirectToRoute('app_facture_show', ['id' => $facture->getId()]);
    }

    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_FACTURATION_VOIR')]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', ['facture' => $facture]);
    }

    #[Route('/{id}/modifier', name: 'app_facture_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_FACTURATION_MODIFIER')]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $em, FactureRepository $factureRepository): Response
    {
        // Seuls les montants/lignes de la dernière facture du contrat sont modifiables.
        $lignesModifiables = $factureRepository->estDerniere($facture);

        $form = $this->createForm(FactureType::class, $facture, ['lignes_modifiables' => $lignesModifiables]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture->appliquerDelaiPaiement();
            $em->flush();
            $this->addFlash('success', 'Facture modifiée.');

            return $this->redirectToRoute('app_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render('facture/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier la facture',
            'lignesModifiables' => $lignesModifiables,
        ]);
    }

    #[Route('/{id}/pdf', name: 'app_facture_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_FACTURATION_VOIR')]
    public function pdf(Facture $facture, PdfGenerator $pdf, SocieteRepository $societeRepository): Response
    {
        return $pdf->reponseDepuisTemplate('pdf/facture.html.twig', [
            'facture' => $facture,
            'societe' => $societeRepository->getSociete(),
        ], ($facture->getNumero() ?? 'facture') . '.pdf');
    }

    #[Route('/{id}/supprimer', name: 'app_facture_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_FACTURATION_SUPPRIMER')]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->request->get('_token'))) {
            $facture->setSupprime(true);
            $em->flush();
            $this->addFlash('success', 'Facture supprimée.');
        }

        return $this->redirectToRoute('app_facture_index');
    }
}
