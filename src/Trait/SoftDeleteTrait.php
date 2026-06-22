<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Suppression logique (soft delete) via un booléen `supprime`.
 * La date et l'auteur de la suppression sont portés par les champs d'audit
 * (modifieLe / modifiePar), une suppression étant considérée comme une modification.
 */
trait SoftDeleteTrait
{
    #[ORM\Column(options: ['default' => false])]
    private bool $supprime = false;

    public function isSupprime(): bool
    {
        return $this->supprime;
    }

    public function setSupprime(bool $supprime): static
    {
        $this->supprime = $supprime;

        return $this;
    }
}
