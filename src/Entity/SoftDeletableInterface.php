<?php

namespace App\Entity;

/**
 * Marque une entité comme "supprimable logiquement" (soft delete).
 * Le filtre Doctrine SoftDeleteFilter exclut automatiquement ces entités
 * lorsqu'elles sont marquées comme supprimées.
 */
interface SoftDeletableInterface
{
    public function getSupprimeLe(): ?\DateTimeImmutable;

    public function setSupprimeLe(?\DateTimeImmutable $supprimeLe): static;

    public function estSupprime(): bool;
}
