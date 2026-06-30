<?php

namespace App\Service;

use App\Repository\SocieteRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Génère une référence de devis/contrat séquentielle, sûre en accès simultané :
 * le compteur (Societe.prochainNumeroContrat) est lu/incrémenté sous verrou pessimiste.
 * Format : préfixe configurable + numéro sur 5 chiffres (ex. DEV-00001).
 */
class ContratNumeroGenerator
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

            $this->em->lock($societe, LockMode::PESSIMISTIC_WRITE);

            $numero = $societe->getProchainNumeroContrat();
            $societe->setProchainNumeroContrat($numero + 1);
            $this->em->flush();

            return sprintf('%s%05d', $societe->getPrefixeContrat(), $numero);
        });
    }
}
