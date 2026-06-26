<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\TypeProduit;
use App\Repository\ProduitRepository;
use App\State\SoftDeleteProcessor;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
// Unicité gérée au niveau applicatif (UniqueEntity) : grâce au filtre soft-delete,
// elle ne s'applique qu'aux produits non supprimés → une référence supprimée est réutilisable.
#[UniqueEntity(fields: ['reference'], message: 'Cette référence existe déjà.')]
#[ApiResource(
    shortName: 'Produit',
    description: 'Articles du catalogue',
    normalizationContext: ['groups' => ['produit:read'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['produit:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_CATALOGUE_VOIR')"),
        new Get(security: "is_granted('ROLE_CATALOGUE_VOIR')"),
        new Post(security: "is_granted('ROLE_CATALOGUE_CREER')"),
        new Patch(security: "is_granted('ROLE_CATALOGUE_MODIFIER')"),
        new Delete(security: "is_granted('ROLE_CATALOGUE_SUPPRIMER')", processor: SoftDeleteProcessor::class),
    ],
)]
class Produit implements SoftDeletableInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['produit:read', 'produit:write', 'contrat:read', 'facture:read'])]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['produit:read', 'produit:write', 'contrat:read', 'facture:read'])]
    private ?string $designation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 20, enumType: TypeProduit::class)]
    #[Groups(['produit:read', 'produit:write'])]
    private TypeProduit $type = TypeProduit::BIEN;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $prixHt = '0.00';

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $tauxTva = '20.00';

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $unite = 'unité';

    /** Si vrai, le stock de ce produit est suivi. */
    #[ORM\Column]
    #[Groups(['produit:read', 'produit:write'])]
    private bool $gereStock = false;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $stockActuel = '0';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3, nullable: true)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $stockMin = null;

    #[ORM\Column]
    #[Groups(['produit:read', 'produit:write'])]
    private bool $actif = true;

    /**
     * Composants (nomenclature) : si non vide, ce produit est "composé".
     *
     * @var Collection<int, Composant>
     */
    #[ORM\OneToMany(targetEntity: Composant::class, mappedBy: 'produitParent', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid]
    #[Groups(['produit:read', 'produit:write'])]
    #[MaxDepth(1)]
    private Collection $composants;

    use TimestampableTrait;

    public function __construct()
    {
        $this->composants = new ArrayCollection();
    }

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
    public function setStockActuel(?string $stockActuel): static { $this->stockActuel = $stockActuel ?? '0'; return $this; }

    public function getStockMin(): ?string { return $this->stockMin; }
    public function setStockMin(?string $stockMin): static { $this->stockMin = $stockMin; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    /** @return Collection<int, Composant> */
    public function getComposants(): Collection { return $this->composants; }

    public function addComposant(Composant $composant): static
    {
        if (!$this->composants->contains($composant)) {
            $this->composants->add($composant);
            $composant->setProduitParent($this);
        }
        return $this;
    }

    public function removeComposant(Composant $composant): static
    {
        if ($this->composants->removeElement($composant) && $composant->getProduitParent() === $this) {
            $composant->setProduitParent(null);
        }
        return $this;
    }

    /** Vrai si ce produit est composé d'autres articles (nomenclature). */
    #[Groups(['produit:read'])]
    public function isCompose(): bool
    {
        return !$this->composants->isEmpty();
    }

    #[Groups(['produit:read'])]
    public function getPrixTtc(): float
    {
        return round((float) $this->prixHt * (1 + (float) $this->tauxTva / 100), 2);
    }

    public function __toString(): string
    {
        return $this->designation ? sprintf('%s (%s)', $this->designation, $this->reference) : 'Produit';
    }
}
