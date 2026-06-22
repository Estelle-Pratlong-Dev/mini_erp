<?php

namespace App\Entity;

/**
 * Marque une entité comme "supprimable logiquement" (soft delete).
 * Le filtre Doctrine SoftDeleteFilter exclut automatiquement les entités
 * dont le booléen `supprime` est à vrai.
 */
interface SoftDeletableInterface
{
    public function isSupprime(): bool;

    public function setSupprime(bool $supprime): static;
}
