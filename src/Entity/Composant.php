<?php

namespace App\Entity;

use App\Repository\ComposantRepository;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Un composant de nomenclature : "ce produit composé contient N unités de cet article".
 * Ex. Bouquet "Compo 1" → 6 × Rose rouge, 4 × Lys blanc, 3 × Gypsophile.
 */
#[ORM\Entity(repositoryClass: ComposantRepository::class)]
class Composant
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    /** Le produit composé qui contient ce composant. */
    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'composants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produitParent = null;

    /** L'article (du catalogue) utilisé comme ingrédient/composant. */
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Choisissez l\'article composant.')]
    #[Groups(['produit:read', 'produit:write'])]
    private ?Produit $composant = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3)]
    #[Assert\Positive]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $quantite = '1';

    public function getId(): ?int { return $this->id; }

    public function getProduitParent(): ?Produit { return $this->produitParent; }
    public function setProduitParent(?Produit $p): static { $this->produitParent = $p; return $this; }

    public function getComposant(): ?Produit { return $this->composant; }
    public function setComposant(?Produit $p): static { $this->composant = $p; return $this; }

    public function getQuantite(): ?string { return $this->quantite; }
    public function setQuantite(string $q): static { $this->quantite = $q; return $this; }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->composant !== null && $this->composant === $this->produitParent) {
            $context->buildViolation('Un produit ne peut pas se contenir lui-même.')
                ->atPath('composant')
                ->addViolation();
        }
    }
}
