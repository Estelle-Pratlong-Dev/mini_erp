<?php

namespace App\Entity;

use App\Enum\TypeProduit;
use App\Repository\ProduitRepository;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PRODUIT_REFERENCE', fields: ['reference'])]
#[UniqueEntity(fields: ['reference'], message: 'Cette référence existe déjà.')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $designation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, enumType: TypeProduit::class)]
    private TypeProduit $type = TypeProduit::BIEN;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $prixHt = '0.00';

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $tauxTva = '20.00';

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $unite = 'unité';

    /** Si vrai, le stock de ce produit est suivi. */
    #[ORM\Column]
    private bool $gereStock = false;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3)]
    private ?string $stockActuel = '0';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3, nullable: true)]
    private ?string $stockMin = null;

    #[ORM\Column]
    private bool $actif = true;

    use TimestampableTrait;

    public function getId(): ?int { return $this->id; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(string $reference): static { $this->reference = $reference; return $this; }

    public function getDesignation(): ?string { return $this->designation; }
    public function setDesignation(string $designation): static { $this->designation = $designation; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getType(): TypeProduit { return $this->type; }
    public function setType(TypeProduit $type): static { $this->type = $type; return $this; }

    public function getPrixHt(): ?string { return $this->prixHt; }
    public function setPrixHt(string $prixHt): static { $this->prixHt = $prixHt; return $this; }

    public function getTauxTva(): ?string { return $this->tauxTva; }
    public function setTauxTva(string $tauxTva): static { $this->tauxTva = $tauxTva; return $this; }

    public function getUnite(): ?string { return $this->unite; }
    public function setUnite(?string $unite): static { $this->unite = $unite; return $this; }

    public function isGereStock(): bool { return $this->gereStock; }
    public function setGereStock(bool $gereStock): static { $this->gereStock = $gereStock; return $this; }

    public function getStockActuel(): ?string { return $this->stockActuel; }
    public function setStockActuel(string $stockActuel): static { $this->stockActuel = $stockActuel; return $this; }

    public function getStockMin(): ?string { return $this->stockMin; }
    public function setStockMin(?string $stockMin): static { $this->stockMin = $stockMin; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getPrixTtc(): float
    {
        return round((float) $this->prixHt * (1 + (float) $this->tauxTva / 100), 2);
    }

    public function __toString(): string
    {
        return $this->designation ? sprintf('%s (%s)', $this->designation, $this->reference) : 'Produit';
    }
}
