<?php

namespace App\Entity;

use App\Repository\LigneDocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ligne d'un document commercial (devis/contrat ; et facture en Phase 3).
 * Réutilisable : un seul des parents (contrat/facture) est renseigné.
 */
#[ORM\Entity(repositoryClass: LigneDocumentRepository::class)]
class LigneDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Contrat $contrat = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Produit $produit = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $designation = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 3)]
    #[Assert\Positive]
    private ?string $quantite = '1';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Assert\PositiveOrZero]
    private ?string $prixUnitaireHt = '0.00';

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $tauxTva = '20.00';

    public function getId(): ?int { return $this->id; }

    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $contrat): static { $this->contrat = $contrat; return $this; }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        // Pré-remplissage pratique depuis le produit
        if ($produit !== null) {
            if (!$this->designation) {
                $this->designation = $produit->getDesignation();
            }
            $this->prixUnitaireHt = $produit->getPrixHt();
            $this->tauxTva = $produit->getTauxTva();
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
