<?php

namespace App\Controller;

use App\Attribute\RequireModule;
use App\Entity\Produit;
use App\Enum\CodeModule;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Service\NomenclatureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produits')]
#[RequireModule(CodeModule::CATALOGUE)]
class ProduitController extends AbstractController
{
    #[Route('', name: 'app_produit_index', methods: ['GET'])]
    #[IsGranted('ROLE_CATALOGUE_VOIR')]
    public function index(ProduitRepository $repository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $repository->findBy([], ['designation' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_produit_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CATALOGUE_CREER')]
    public function new(Request $request, EntityManagerInterface $em, NomenclatureService $nomenclature): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($produit->isCompose()) {
                $produit->setPrixAchatHt((string) $nomenclature->prixAchatCompose($produit));
            }
            $em->persist($produit);
            $em->flush();
            $this->addFlash('success', 'Produit créé.');

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/form.html.twig', [
            'form' => $form,
            'titre' => 'Nouveau produit',
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CATALOGUE_MODIFIER')]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em, NomenclatureService $nomenclature): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($produit->isCompose()) {
                $produit->setPrixAchatHt((string) $nomenclature->prixAchatCompose($produit));
            }
            $em->flush();
            $this->addFlash('success', 'Produit modifié.');

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier le produit',
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_produit_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CATALOGUE_SUPPRIMER')]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $produit->setSupprime(true);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('app_produit_index');
    }
}
