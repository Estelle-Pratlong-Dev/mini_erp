<?php

namespace App\Entity;

use App\Repository\UniteProduitRepository;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unité d'un produit (liste de référence personnalisable depuis l'admin).
 * Ex. pièce, botte, tige, kg, g, mètre, lot…
 */
#[ORM\Entity(repositoryClass: UniteProduitRepository::class)]
#[UniqueEntity(fields: ['nom'], message: 'Cette unité existe déjà.')]
class UniteProduit implements SoftDeletableInterface
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['produit:read'])]
    private ?string $nom = null;

    #[ORM\Column]
    private bool $actif = true;

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function __toString(): string { return $this->nom ?? 'Unité'; }
}
