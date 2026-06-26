<?php

namespace App\Service;

use App\Repository\SocieteRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Génère un numéro de facture séquentiel, sans trou et sûr en cas d'accès simultanés :
 * le compteur (Societe.prochainNumeroFacture) est lu/incrémenté sous verrou pessimiste,
 * dans une transaction.
 */
class FactureNumeroGenerator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SocieteRepository $societeRepository,
    ) {
    }

    public function generer(): string
    {
        return $this->em->wrapInTransaction(function () {
            $societe = $this->societeRepository->getSociete();
            if ($societe === null) {
                throw new \RuntimeException('Société non configurée : exécutez app:install.');
            }

            // Verrou pessimiste sur la ligne société pour sérialiser l'incrément.
            $this->em->lock($societe, LockMode::PESSIMISTIC_WRITE);

            $numero = $societe->getProchainNumeroFacture();
            $societe->setProchainNumeroFacture($numero + 1);
            $this->em->flush();

            return sprintf('%s%05d', $societe->getPrefixeFacture(), $numero);
        });
    }
}
