<?php

namespace App\Entity;

use App\Repository\TauxTvaRepository;
use App\Trait\SoftDeleteTrait;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Taux de TVA (liste de référence personnalisable depuis l'admin).
 * Ex. 20 % (normal), 10 %, 5,5 %, 2,1 %, 0 % (exonéré).
 * La valeur décimale est recopiée (snapshot) sur les produits / lignes : modifier ou
 * désactiver un taux ici n'altère pas les documents déjà enregistrés.
 */
#[ORM\Entity(repositoryClass: TauxTvaRepository::class)]
#[UniqueEntity(fields: ['taux'], message: 'Ce taux de TVA existe déjà.')]
class TauxTva implements SoftDeletableInterface
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?string $taux = '20.00';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column]
    private bool $actif = true;

    public function getId(): ?int { return $this->id; }

    public function getTaux(): ?string { return $this->taux; }
    public function setTaux(string $taux): static { $this->taux = $taux; return $this; }

    public function getLibelle(): ?string { return $this->libelle; }
    public function setLibelle(?string $libelle): static { $this->libelle = $libelle; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    /** Libellé pour les listes déroulantes, ex. « 20 % (Normal) ». */
    public function getLibelleAffiche(): string
    {
        $taux = rtrim(rtrim(number_format((float) $this->taux, 2, ',', ' '), '0'), ',');

        return $taux . ' %' . ($this->libelle ? ' (' . $this->libelle . ')' : '');
    }

    public function __toString(): string { return $this->getLibelleAffiche(); }
}
