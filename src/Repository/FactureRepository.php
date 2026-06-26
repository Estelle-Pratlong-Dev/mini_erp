<?php

namespace App\Repository;

use App\Entity\Contrat;
use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    /**
     * Factures d'un contrat (les supprimées sont exclues par le filtre soft-delete).
     *
     * @return Facture[]
     */
    public function duContrat(Contrat $contrat): array
    {
        return $this->findBy(['contrat' => $contrat], ['id' => 'ASC']);
    }

    /**
     * Vrai si la facture est la plus récente de son contrat (donc modifiable).
     * Une facture sans contrat (directe) est toujours considérée modifiable.
     */
    public function estDerniere(Facture $facture): bool
    {
        $contrat = $facture->getContrat();
        if ($contrat === null) {
            return true;
        }

        $factures = $this->duContrat($contrat);
        if ($factures === []) {
            return true;
        }

        $derniere = end($factures);

        return $derniere->getId() === $facture->getId();
    }
}
