<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\PieceJointe;
use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des pièces jointes (rattachées à un projet ou un contrat).
 * Les fichiers sont stockés hors racine web (var/uploads/pieces) et servis ici sous contrôle d'accès.
 */
#[Route('/pieces')]
#[IsGranted('ROLE_USER')]
class PieceJointeController extends AbstractController
{
    private function uploadDir(): string
    {
        return $this->getParameter('kernel.project_dir') . '/var/uploads/pieces';
    }

    #[Route('/upload', name: 'app_piece_upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('upload_piece', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('fichier');
        $cible = $request->request->get('cible');
        $cibleId = $request->request->getInt('cible_id');

        if (!$file) {
            $this->addFlash('warning', 'Aucun fichier sélectionné.');

            return $this->redirectToReferer($request, $cible, $cibleId);
        }

        $piece = new PieceJointe();
        $piece->setNomOriginal($file->getClientOriginalName());
        $piece->setTypeMime($file->getClientMimeType());
        $piece->setTaille($file->getSize());

        $nomStocke = uniqid('pj_', true) . '.' . ($file->guessExtension() ?: 'bin');
        try {
            $file->move($this->uploadDir(), $nomStocke);
        } catch (FileException) {
            $this->addFlash('error', 'Échec de l\'enregistrement du fichier.');

            return $this->redirectToReferer($request, $cible, $cibleId);
        }
        $piece->setFichier($nomStocke);

        if ($cible === 'projet' && $cibleId) {
            $projet = $em->getRepository(Projet::class)->find($cibleId);
            if ($projet) {
                $piece->setProjet($projet);
            }
        } elseif ($cible === 'contrat' && $cibleId) {
            $contrat = $em->getRepository(Contrat::class)->find($cibleId);
            if ($contrat) {
                $piece->setContrat($contrat);
            }
        }

        $em->persist($piece);
        $em->flush();
        $this->addFlash('success', 'Pièce jointe ajoutée.');

        return $this->redirectToReferer($request, $cible, $cibleId);
    }

    #[Route('/{id}/telecharger', name: 'app_piece_download', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function download(PieceJointe $piece): Response
    {
        $chemin = $this->uploadDir() . '/' . $piece->getFichier();
        if (!is_file($chemin)) {
            throw $this->createNotFoundException('Fichier introuvable.');
        }

        $response = new BinaryFileResponse($chemin);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $piece->getNomOriginal() ?? $piece->getFichier()
        );

        return $response;
    }

    #[Route('/{id}/supprimer', name: 'app_piece_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, PieceJointe $piece, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_piece' . $piece->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $cible = $piece->getContrat() ? 'contrat' : 'projet';
        $cibleId = $piece->getContrat()?->getId() ?? $piece->getProjet()?->getId() ?? 0;

        // Suppression logique : on conserve le fichier et la ligne en base.
        $piece->setSupprime(true);
        $em->flush();
        $this->addFlash('success', 'Pièce jointe supprimée.');

        return $this->redirectToReferer($request, $cible, $cibleId);
    }

    private function redirectToReferer(Request $request, ?string $cible, int $cibleId): Response
    {
        if ($cible === 'contrat' && $cibleId) {
            return $this->redirectToRoute('app_contrat_show', ['id' => $cibleId]);
        }
        if ($cible === 'projet' && $cibleId) {
            return $this->redirectToRoute('app_projet_show', ['id' => $cibleId]);
        }

        return $this->redirectToRoute('app_home');
    }
}
