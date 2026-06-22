<?php

namespace App\Entity;

use App\Enum\StatutProjet;
use App\Repository\ProjetRepository;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Conteneur de regroupement : un projet porte des contrats (devis/contrats) et des factures.
 * Le nom est libre (nom du client, nom du marché, ou date du jour pour les ventes du jour).
 */
#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet implements SoftDeletableInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Contact $contact = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 20, enumType: StatutProjet::class)]
    private StatutProjet $statut = StatutProjet::EN_COURS;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** @var Collection<int, Contrat> */
    #[ORM\OneToMany(targetEntity: Contrat::class, mappedBy: 'projet', orphanRemoval: true)]
    private Collection $contrats;

    /** @var Collection<int, PieceJointe> */
    #[ORM\OneToMany(targetEntity: PieceJointe::class, mappedBy: 'projet', orphanRemoval: true)]
    private Collection $piecesJointes;

    use TimestampableTrait;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
        $this->piecesJointes = new ArrayCollection();
        $this->date = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getContact(): ?Contact { return $this->contact; }
    public function setContact(?Contact $contact): static { $this->contact = $contact; return $this; }

    public function getDate(): ?\DateTimeImmutable { return $this->date; }
    public function setDate(\DateTimeImmutable $date): static { $this->date = $date; return $this; }

    public function getStatut(): StatutProjet { return $this->statut; }
    public function setStatut(StatutProjet $statut): static { $this->statut = $statut; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    /** @return Collection<int, Contrat> */
    public function getContrats(): Collection { return $this->contrats; }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setProjet($this);
        }
        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat) && $contrat->getProjet() === $this) {
            $contrat->setProjet(null);
        }
        return $this;
    }

    /** @return Collection<int, PieceJointe> */
    public function getPiecesJointes(): Collection { return $this->piecesJointes; }

    public function addPieceJointe(PieceJointe $piece): static
    {
        if (!$this->piecesJointes->contains($piece)) {
            $this->piecesJointes->add($piece);
            $piece->setProjet($this);
        }
        return $this;
    }

    public function removePieceJointe(PieceJointe $piece): static
    {
        if ($this->piecesJointes->removeElement($piece) && $piece->getProjet() === $this) {
            $piece->setProjet(null);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? 'Projet';
    }
}
