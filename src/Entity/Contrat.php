<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\StatutContrat;
use App\Enum\TypeContrat;
use App\Repository\ContratRepository;
use App\State\SoftDeleteProcessor;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Devis ou contrat rattaché à un projet. Composé de lignes (LigneArticle).
 * Convertible en facture (Phase 3).
 */
#[ORM\Entity(repositoryClass: ContratRepository::class)]
#[ApiResource(
    shortName: 'Contrat',
    description: 'Devis / contrats (avec leurs lignes d\'articles)',
    normalizationContext: ['groups' => ['contrat:read']],
    denormalizationContext: ['groups' => ['contrat:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_CONTRATS_VOIR')"),
        new Get(security: "is_granted('ROLE_CONTRATS_VOIR')"),
        new Post(security: "is_granted('ROLE_CONTRATS_CREER')"),
        new Patch(security: "is_granted('ROLE_CONTRATS_MODIFIER')"),
        new Delete(security: "is_granted('ROLE_CONTRATS_SUPPRIMER')", processor: SoftDeleteProcessor::class),
    ],
)]
class Contrat implements SoftDeletableInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contrat:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['contrat:read', 'contrat:write'])]
    private ?string $reference = null;

    #[ORM\ManyToOne(targetEntity: Projet::class, inversedBy: 'contrats')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['contrat:read', 'contrat:write'])]
    private ?Projet $projet = null;

    /** Client du document (peut différer du contact du projet). */
    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['contrat:read', 'contrat:write'])]
    private ?Contact $contact = null;

    #[ORM\Column(length: 20, enumType: TypeContrat::class)]
    #[Groups(['contrat:read', 'contrat:write'])]
    private TypeContrat $type = TypeContrat::DEVIS;

    #[ORM\Column(length: 20, enumType: StatutContrat::class)]
    #[Groups(['contrat:read', 'contrat:write'])]
    private StatutContrat $statut = StatutContrat::EN_ATTENTE;

    #[ORM\Column(type: 'date_immutable')]
    #[Groups(['contrat:read', 'contrat:write'])]
    private ?\DateTimeImmutable $dateEmission = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Groups(['contrat:read', 'contrat:write'])]
    private ?\DateTimeImmutable $dateValidite = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['contrat:read', 'contrat:write'])]
    private ?string $notes = null;

    /** @var Collection<int, LigneArticle> */
    #[ORM\OneToMany(targetEntity: LigneArticle::class, mappedBy: 'contrat', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid]
    #[Assert\Count(min: 1, minMessage: 'Ajoutez au moins une ligne (un article du catalogue).')]
    #[Groups(['contrat:read', 'contrat:write'])]
    private Collection $lignes;

    /** @var Collection<int, PieceJointe> */
    #[ORM\OneToMany(targetEntity: PieceJointe::class, mappedBy: 'contrat', orphanRemoval: true)]
    private Collection $piecesJointes;

    use TimestampableTrait;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->piecesJointes = new ArrayCollection();
        $this->dateEmission = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $reference): static { $this->reference = $reference; return $this; }

    public function getProjet(): ?Projet { return $this->projet; }
    public function setProjet(?Projet $projet): static { $this->projet = $projet; return $this; }

    public function getContact(): ?Contact { return $this->contact; }
    public function setContact(?Contact $contact): static { $this->contact = $contact; return $this; }

    public function getType(): TypeContrat { return $this->type; }
    public function setType(TypeContrat $type): static { $this->type = $type; return $this; }

    public function getStatut(): StatutContrat { return $this->statut; }
    public function setStatut(StatutContrat $statut): static { $this->statut = $statut; return $this; }

    public function getDateEmission(): ?\DateTimeImmutable { return $this->dateEmission; }
    public function setDateEmission(\DateTimeImmutable $d): static { $this->dateEmission = $d; return $this; }

    public function getDateValidite(): ?\DateTimeImmutable { return $this->dateValidite; }
    public function setDateValidite(?\DateTimeImmutable $d): static { $this->dateValidite = $d; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static { $this->notes = $notes; return $this; }

    /** @return Collection<int, LigneArticle> */
    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(LigneArticle $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setContrat($this);
        }
        return $this;
    }

    public function removeLigne(LigneArticle $ligne): static
    {
        if ($this->lignes->removeElement($ligne) && $ligne->getContrat() === $this) {
            $ligne->setContrat(null);
        }
        return $this;
    }

    /** @return Collection<int, PieceJointe> */
    public function getPiecesJointes(): Collection { return $this->piecesJointes; }

    public function addPieceJointe(PieceJointe $piece): static
    {
        if (!$this->piecesJointes->contains($piece)) {
            $this->piecesJointes->add($piece);
            $piece->setContrat($this);
        }
        return $this;
    }

    public function removePieceJointe(PieceJointe $piece): static
    {
        if ($this->piecesJointes->removeElement($piece) && $piece->getContrat() === $this) {
            $piece->setContrat(null);
        }
        return $this;
    }

    #[Groups(['contrat:read'])]
    public function getTotalHt(): float
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->getMontantHt();
        }
        return round($total, 2);
    }

    #[Groups(['contrat:read'])]
    public function getTotalTva(): float
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->getMontantTva();
        }
        return round($total, 2);
    }

    #[Groups(['contrat:read'])]
    public function getTotalTtc(): float
    {
        return round($this->getTotalHt() + $this->getTotalTva(), 2);
    }

    public function __toString(): string
    {
        return ($this->reference ?: $this->type->libelle()) . ' #' . ($this->id ?? '?');
    }
}
