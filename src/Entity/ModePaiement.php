<?php

namespace App\Entity;

use App\Repository\ModePaiementRepository;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mode de paiement (liste de référence personnalisable depuis l'admin).
 */
#[ORM\Entity(repositoryClass: ModePaiementRepository::class)]
#[UniqueEntity(fields: ['nom'], message: 'Ce mode de paiement existe déjà.')]
class ModePaiement implements SoftDeletableInterface
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['facture:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['facture:read'])]
    private ?string $nom = null;

    #[ORM\Column]
    private bool $actif = true;

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function __toString(): string { return $this->nom ?? 'Mode de paiement'; }
}
