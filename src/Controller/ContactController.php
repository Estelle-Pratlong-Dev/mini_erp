<?php

namespace App\Controller;

use App\Attribute\RequireModule;
use App\Entity\Contact;
use App\Enum\CodeModule;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contacts')]
#[RequireModule(CodeModule::CONTACTS)]
class ContactController extends AbstractController
{
    #[Route('', name: 'app_contact_index', methods: ['GET'])]
    #[IsGranted('ROLE_CONTACTS_VOIR')]
    public function index(ContactRepository $repository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_contact_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTACTS_CREER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();
            $this->addFlash('success', 'Contact créé.');

            return $this->redirectToRoute('app_contact_index');
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form,
            'titre' => 'Nouveau contact',
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONTACTS_MODIFIER')]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Contact modifié.');

            return $this->redirectToRoute('app_contact_index');
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier le contact',
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_contact_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CONTACTS_SUPPRIMER')]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $em->remove($contact);
            $em->flush();
            $this->addFlash('success', 'Contact supprimé.');
        }

        return $this->redirectToRoute('app_contact_index');
    }
}
