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
     * Rang (1-based) de la facture parmi celles de son contrat. null si facture directe.
     */
    public function rangDansContrat(Facture $facture): ?int
    {
        $contrat = $facture->getContrat();
        if ($contrat === null) {
            return null;
        }
        foreach ($this->duContrat($contrat) as $i => $f) {
            if ($f->getId() === $facture->getId()) {
                return $i + 1;
            }
        }

        return null;
    }

    /** Nombre total de factures du contrat de cette facture (0 si facture directe). */
    public function nombreDuContrat(Facture $facture): int
    {
        $contrat = $facture->getContrat();

        return $contrat === null ? 0 : count($this->duContrat($contrat));
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
