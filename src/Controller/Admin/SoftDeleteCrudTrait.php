<?php

namespace App\Controller\Admin;

use App\Entity\SoftDeletableInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Remplace la suppression EasyAdmin par une suppression logique (soft delete)
 * pour les entités qui implémentent SoftDeletableInterface.
 */
trait SoftDeleteCrudTrait
{
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof SoftDeletableInterface) {
            $entityInstance->setSupprime(true);
            $entityManager->flush();

            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }
}
