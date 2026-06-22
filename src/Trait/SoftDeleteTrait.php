<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ajoute une colonne `supprime_le` pour la suppression logique (soft delete).
 * Un élément est considéré supprimé si `supprimeLe` n'est pas nul.
 */
trait SoftDeleteTrait
{
    #[ORM\Column(name: 'supprime_le', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $supprimeLe = null;

    public function getSupprimeLe(): ?\DateTimeImmutable
    {
        return $this->supprimeLe;
    }

    public function setSupprimeLe(?\DateTimeImmutable $supprimeLe): static
    {
        $this->supprimeLe = $supprimeLe;

        return $this;
    }

    public function estSupprime(): bool
    {
        return $this->supprimeLe !== null;
    }

    /** Marque l'élément comme supprimé (maintenant). */
    public function marquerCommeSupprime(): static
    {
        $this->supprimeLe = new \DateTimeImmutable();

        return $this;
    }
}
