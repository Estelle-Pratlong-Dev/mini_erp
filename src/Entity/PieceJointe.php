<?php

namespace App\Entity;

use App\Repository\PieceJointeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Document joint (mail, PDF, image, doc généré…) rattaché à un projet et/ou un contrat
 * (et une facture en Phase 3). Le fichier est stocké hors web et servi via un contrôleur sécurisé.
 */
#[ORM\Entity(repositoryClass: PieceJointeRepository::class)]
class PieceJointe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Nom du fichier stocké sur le disque (unique). */
    #[ORM\Column(length: 255)]
    private ?string $fichier = null;

    #[ORM\Column(length: 255)]
    private ?string $nomOriginal = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $typeMime = null;

    #[ORM\Column(nullable: true)]
    private ?int $taille = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dateAjout = null;

    #[ORM\ManyToOne(targetEntity: Projet::class, inversedBy: 'piecesJointes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Projet $projet = null;

    #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: 'piecesJointes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Contrat $contrat = null;

    public function __construct()
    {
        $this->dateAjout = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getFichier(): ?string { return $this->fichier; }
    public function setFichier(string $fichier): static { $this->fichier = $fichier; return $this; }

    public function getNomOriginal(): ?string { return $this->nomOriginal; }
    public function setNomOriginal(string $nom): static { $this->nomOriginal = $nom; return $this; }

    public function getTypeMime(): ?string { return $this->typeMime; }
    public function setTypeMime(?string $type): static { $this->typeMime = $type; return $this; }

    public function getTaille(): ?int { return $this->taille; }
    public function setTaille(?int $taille): static { $this->taille = $taille; return $this; }

    public function getDateAjout(): ?\DateTimeImmutable { return $this->dateAjout; }
    public function setDateAjout(\DateTimeImmutable $d): static { $this->dateAjout = $d; return $this; }

    public function getProjet(): ?Projet { return $this->projet; }
    public function setProjet(?Projet $projet): static { $this->projet = $projet; return $this; }

    public function getContrat(): ?Contrat { return $this->contrat; }
    public function setContrat(?Contrat $contrat): static { $this->contrat = $contrat; return $this; }

    public function __toString(): string
    {
        return $this->nomOriginal ?? 'Pièce jointe';
    }
}
