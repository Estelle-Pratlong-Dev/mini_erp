<?php

namespace App\Entity;

use App\Repository\LigneArticleRepository;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ligne d'article d'un document commercial (devis/contrat ; et facture en Phase 3).
 * Chaque ligne référence un article du catalogue. Réutilisable : un seul des
 * parents (contrat/facture) est renseigné.
 */
#[ORM\Entity(repositoryClass: LigneArticleRepository::class)]
class LigneArticle
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contrat:read', 'facture:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Contrat $contrat = null;

    #[ORM\ManyToOne(targetEntity: Facture::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Facture $facture = null;

    /** Ligne du contrat d'origine (référence) quand cette ligne facture un contrat. */
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?self $ligneSource = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Chaque ligne doit référencer un article du catalogue.')]
    #[Groups(['contrat:read', 'contrat:write', 'facture:read', 'facture:write'])]
    private ?Produit $produit = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['contrat:read', 'facture:read'])]
    private ?string $designation = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3)]
    #[Assert\Positive]
    #[Groups(['contrat:read', 'contrat:write', 'facture:read', 'facture:write'])]
    private ?string $quantite = '1';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Assert\PositiveOrZero]
    #[Groups(['contrat:read', 'contrat:write', 'facture:read', 'facture:write'])]
    private ?string $prixUnitaireHt = '0.00';

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Groups(['contrat:read', 'contrat:write', 'facture:read', 'facture:write'])]
    private ?string $tauxTva = '20.00';

    public function getId(): ?int { return $this->id; }

    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $contrat): static { $this->contrat = $contrat; return $this; }

    public function getFacture(): ?Facture { return $this->facture; }
    public function setFacture(?Facture $facture): static { $this->facture = $facture; return $this; }

    public function getLigneSource(): ?self { return $this->ligneSource; }
    public function setLigneSource(?self $ligne): static { $this->ligneSource = $ligne; return $this; }

    /** Quantité prévue au contrat (référence), si cette ligne provient d'un contrat. */
    #[Groups(['facture:read'])]
    public function getQuantiteContrat(): ?string
    {
        return $this->ligneSource?->getQuantite();
    }

    /** Montant HT prévu au contrat (référence), si cette ligne provient d'un contrat. */
    #[Groups(['facture:read'])]
    public function getMontantContrat(): ?float
    {
        return $this->ligneSource?->getMontantHt();
    }

    /** Pourcentage facturé par rapport à la quantité prévue au contrat. */
    #[Groups(['facture:read'])]
    public function getPourcentageFacture(): ?float
    {
        $qteContrat = (float) ($this->ligneSource?->getQuantite() ?? 0);
        if ($qteContrat == 0.0) {
            return null;
        }

        return round((float) $this->quantite / $qteContrat * 100, 2);
    }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        // La désignation, le prix et la TVA proviennent de l'article du catalogue.
        if ($produit !== null) {
            $this->designation = $produit->getDesignation();
            if ($this->prixUnitaireHt === null || (float) $this->prixUnitaireHt === 0.0) {
                $this->prixUnitaireHt = $produit->getPrixHt();
            }
            if ($this->tauxTva === null) {
                $this->tauxTva = $produit->getTauxTva();
            }
        }
        return $this;
    }

    public function getDesignation(): ?string { return $this->designation; }
    public function setDesignation(string $designation): static { $this->designation = $designation; return $this; }

    public function getQuantite(): ?string { return $this->quantite; }
    public function setQuantite(string $quantite): static { $this->quantite = $quantite; return $this; }

    public function getPrixUnitaireHt(): ?string { return $this->prixUnitaireHt; }
    public function setPrixUnitaireHt(string $prix): static { $this->prixUnitaireHt = $prix; return $this; }

    public function getTauxTva(): ?string { return $this->tauxTva; }
    public function setTauxTva(string $taux): static { $this->tauxTva = $taux; return $this; }

    #[Groups(['contrat:read', 'facture:read'])]
    public function getMontantHt(): float
    {
        return round((float) $this->quantite * (float) $this->prixUnitaireHt, 2);
    }

    public function getMontantTva(): float
    {
        return round($this->getMontantHt() * (float) $this->tauxTva / 100, 2);
    }

    public function getMontantTtc(): float
    {
        return round($this->getMontantHt() + $this->getMontantTva(), 2);
    }
}
