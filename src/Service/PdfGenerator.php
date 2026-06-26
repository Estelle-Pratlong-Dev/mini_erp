<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Génération de PDF isolée derrière ce service (façade Dompdf).
 * Si on change de moteur PDF un jour, seul ce fichier est impacté.
 */
class PdfGenerator
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Rend un template Twig en PDF et renvoie une réponse téléchargeable.
     *
     * @param array<string, mixed> $context
     */
    public function reponseDepuisTemplate(string $template, array $context, string $nomFichier): Response
    {
        $html = $this->twig->render($template, $context);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('inline; filename="%s"', $nomFichier),
            ]
        );
    }
}
