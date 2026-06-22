<?php

namespace App\Doctrine;

use App\Entity\SoftDeletableInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Filtre Doctrine global : exclut automatiquement les entités marquées comme
 * supprimées (soft delete) de toutes les requêtes ORM (findBy, count, DQL, associations).
 */
class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(SoftDeletableInterface::class)) {
            return '';
        }

        $column = $targetEntity->getColumnName('supprimeLe');

        return sprintf('%s.%s IS NULL', $targetTableAlias, $column);
    }
}
