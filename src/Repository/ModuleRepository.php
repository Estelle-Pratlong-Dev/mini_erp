<?php

namespace App\Repository;

use App\Entity\Module;
use App\Enum\CodeModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    /**
     * Retourne la liste des codes de modules actifs.
     *
     * @return array<string, bool> map code => true
     */
    public function getActiveCodesMap(): array
    {
        $rows = $this->createQueryBuilder('m')
            ->select('m.code', 'm.actif')
            ->getQuery()
            ->getArrayResult();

        $map = [];
        foreach ($rows as $row) {
            $code = $row['code'] instanceof CodeModule ? $row['code']->value : (string) $row['code'];
            $map[$code] = (bool) $row['actif'];
        }

        return $map;
    }
}
