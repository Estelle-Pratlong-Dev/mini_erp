<?php

namespace App\Controller;

use App\Attribute\RequireModule;
use App\Entity\Projet;
use App\Enum\CodeModule;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projets')]
#[RequireModule(CodeModule::PROJETS)]
class ProjetController extends AbstractController
{
    #[Route('', name: 'app_projet_index', methods: ['GET'])]
    #[IsGranted('ROLE_PROJETS_VOIR')]
    public function index(ProjetRepository $repository): Response
    {
        return $this->render('projet/index.html.twig', [
            'projets' => $repository->findBy([], ['date' => 'DESC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_projet_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PROJETS_CREER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($projet);
            $em->flush();
            $this->addFlash('success', 'Projet créé.');

            return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
        }

        return $this->render('projet/form.html.twig', ['form' => $form, 'titre' => 'Nouveau projet']);
    }

    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_PROJETS_VOIR')]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', ['projet' => $projet]);
    }

    #[Route('/{id}/modifier', name: 'app_projet_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_PROJETS_MODIFIER')]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Projet modifié.');

            return $this->redirectToRoute('app_projet_show', ['id' => $projet->getId()]);
        }

        return $this->render('projet/form.html.twig', ['form' => $form, 'titre' => 'Modifier le projet']);
    }

    #[Route('/{id}/supprimer', name: 'app_projet_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_PROJETS_SUPPRIMER')]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->request->get('_token'))) {
            $projet->setSupprimeLe(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Projet supprimé.');
        }

        return $this->redirectToRoute('app_projet_index');
    }
}
