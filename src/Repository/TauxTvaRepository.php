<?php

namespace App\Repository;

use App\Entity\TauxTva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TauxTva>
 */
class TauxTvaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TauxTva::class);
    }

    /**
     * Taux actifs (du plus élevé au plus faible).
     *
     * @return TauxTva[]
     */
    public function actifs(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.actif = true')
            ->orderBy('t.taux', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
