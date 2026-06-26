<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\DelaiPaiement;
use App\Enum\ModePaiement;
use App\Enum\StatutFacture;
use App\Repository\FactureRepository;
use App\State\FacturePersistProcessor;
use App\State\SoftDeleteProcessor;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Facture rattachée à un projet (jamais orpheline), éventuellement issue d'un contrat.
 * Un avoir est une facture à montant négatif (pas de type distinct : seul l'affichage change).
 */
#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ApiResource(
    shortName: 'Facture',
    description: 'Factures et avoirs (avoir = montant négatif)',
    normalizationContext: ['groups' => ['facture:read']],
    denormalizationContext: ['groups' => ['facture:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_FACTURATION_VOIR')"),
        new Get(security: "is_granted('ROLE_FACTURATION_VOIR')"),
        new Post(security: "is_granted('ROLE_FACTURATION_CREER')", processor: FacturePersistProcessor::class),
        new Patch(security: "is_granted('ROLE_FACTURATION_MODIFIER')"),
        new Delete(security: "is_granted('ROLE_FACTURATION_SUPPRIMER')", processor: SoftDeleteProcessor::class),
    ],
)]
class Facture implements SoftDeletableInterface
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['facture:read'])]
    private ?int $id = null;

    /** Numéro séquentiel attribué à la création (via FactureNumeroGenerator). */
    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['facture:read'])]
    private ?string $numero = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['facture:read', 'facture:write'])]
    private ?Projet $projet = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?Contact $contact = null;

    /** Contrat d'origine si la facture en est issue. */
    #[ORM\ManyToOne(targetEntity: Contrat::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?Contrat $contrat = null;

    #[ORM\Column(length: 20, enumType: StatutFacture::class)]
    #[Groups(['facture:read', 'facture:write'])]
    private StatutFacture $statut = StatutFacture::EN_ATTENTE;

    #[ORM\Column(type: 'date_immutable')]
    #[Groups(['facture:read', 'facture:write'])]
    private ?\DateTimeImmutable $dateEmission = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?\DateTimeImmutable $dateEcheance = null;

    #[ORM\Column(type: 'integer', nullable: true, enumType: DelaiPaiement::class)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?DelaiPaiement $delaiPaiement = null;

    #[ORM\Column(length: 20, nullable: true, enumType: ModePaiement::class)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?ModePaiement $modePaiement = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['facture:read', 'facture:write'])]
    private ?string $notes = null;

    /** @var Collection<int, LigneArticle> */
    #[ORM\OneToMany(targetEntity: LigneArticle::class, mappedBy: 'facture', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid]
    #[Assert\Count(min: 1, minMessage: 'Ajoutez au moins une ligne (un article du catalogue).')]
    #[Groups(['facture:read', 'facture:write'])]
    private Collection $lignes;

    /** @var Collection<int, PieceJointe> */
    #[ORM\OneToMany(targetEntity: PieceJointe::class, mappedBy: 'facture', orphanRemoval: true)]
    private Collection $piecesJointes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->piecesJointes = new ArrayCollection();
        $this->dateEmission = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(?string $numero): static { $this->numero = $numero; return $this; }

    public function getProjet(): ?Projet { return $this->projet; }
    public function setProjet(?Projet $projet): static { $this->projet = $projet; return $this; }

    public function getContact(): ?Contact { return $this->contact; }
    public function setContact(?Contact $contact): static { $this->contact = $contact; return $this; }

    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $contrat): static { $this->contrat = $contrat; return $this; }

    public function getStatut(): StatutFacture { return $this->statut; }
    public function setStatut(StatutFacture $statut): static { $this->statut = $statut; return $this; }

    public function getDateEmission(): ?\DateTimeImmutable { return $this->dateEmission; }
    public function setDateEmission(\DateTimeImmutable $d): static { $this->dateEmission = $d; return $this; }

    public function getDateEcheance(): ?\DateTimeImmutable { return $this->dateEcheance; }
    public function setDateEcheance(?\DateTimeImmutable $d): static { $this->dateEcheance = $d; return $this; }

    public function getDelaiPaiement(): ?DelaiPaiement { return $this->delaiPaiement; }
    public function setDelaiPaiement(?DelaiPaiement $delai): static { $this->delaiPaiement = $delai; return $this; }

    public function getModePaiement(): ?ModePaiement { return $this->modePaiement; }
    public function setModePaiement(?ModePaiement $mode): static { $this->modePaiement = $mode; return $this; }

    /** Recalcule la date d'échéance à partir de la date d'émission et du délai de paiement. */
    public function appliquerDelaiPaiement(): void
    {
        if ($this->delaiPaiement !== null && $this->dateEmission !== null) {
            $this->dateEcheance = $this->dateEmission->modify('+' . $this->delaiPaiement->value . ' days');
        }
    }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static { $this->notes = $notes; return $this; }

    /** @return Collection<int, LigneArticle> */
    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(LigneArticle $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setFacture($this);
        }
        return $this;
    }

    public function removeLigne(LigneArticle $ligne): static
    {
        if ($this->lignes->removeElement($ligne) && $ligne->getFacture() === $this) {
            $ligne->setFacture(null);
        }
        return $this;
    }

    /** @return Collection<int, PieceJointe> */
    public function getPiecesJointes(): Collection { return $this->piecesJointes; }

    public function addPieceJointe(PieceJointe $piece): static
    {
        if (!$this->piecesJointes->contains($piece)) {
            $this->piecesJointes->add($piece);
            $piece->setFacture($this);
        }
        return $this;
    }

    public function removePieceJointe(PieceJointe $piece): static
    {
        if ($this->piecesJointes->removeElement($piece) && $piece->getFacture() === $this) {
            $piece->setFacture(null);
        }
        return $this;
    }

    #[Groups(['facture:read'])]
    public function getTotalHt(): float
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->getMontantHt();
        }
        return round($total, 2);
    }

    #[Groups(['facture:read'])]
    public function getTotalTva(): float
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->getMontantTva();
        }
        return round($total, 2);
    }

    #[Groups(['facture:read'])]
    public function getTotalTtc(): float
    {
        return round($this->getTotalHt() + $this->getTotalTva(), 2);
    }

    /** Un avoir est une facture dont le total est négatif. */
    #[Groups(['facture:read'])]
    public function isAvoir(): bool
    {
        return $this->getTotalTtc() < 0;
    }

    /** Libellé du document selon le signe : « Facture » ou « Avoir ». */
    #[Groups(['facture:read'])]
    public function getLibelleType(): string
    {
        return $this->isAvoir() ? 'Avoir' : 'Facture';
    }

    public function __toString(): string
    {
        return ($this->numero ?: $this->getLibelleType()) . ' #' . ($this->id ?? '?');
    }
}
