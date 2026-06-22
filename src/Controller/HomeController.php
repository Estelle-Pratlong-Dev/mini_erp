<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use App\Repository\ProduitRepository;
use App\Service\ModuleManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ModuleManager $moduleManager,
        ContactRepository $contactRepository,
        ProduitRepository $produitRepository,
    ): Response {
        $stats = [];
        if ($moduleManager->isEnabled('CONTACTS')) {
            $stats['contacts'] = $contactRepository->count([]);
        }
        if ($moduleManager->isEnabled('CATALOGUE')) {
            $stats['produits'] = $produitRepository->count([]);
        }

        return $this->render('home/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
