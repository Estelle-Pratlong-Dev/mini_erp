<?php

namespace App\Trait;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Champs d'audit : qui a créé/modifié et quand. Remplis automatiquement
 * par App\EventListener\TimestampableListener.
 */
trait TimestampableTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $creePar = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $modifiePar = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $creeLe = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modifieLe = null;

    public function getCreePar(): ?User { return $this->creePar; }
    public function setCreePar(?User $user): static { $this->creePar = $user; return $this; }

    public function getModifiePar(): ?User { return $this->modifiePar; }
    public function setModifiePar(?User $user): static { $this->modifiePar = $user; return $this; }

    public function getCreeLe(): ?\DateTimeImmutable { return $this->creeLe; }
    public function setCreeLe(\DateTimeImmutable $dt): static { $this->creeLe = $dt; return $this; }

    public function getModifieLe(): ?\DateTimeImmutable { return $this->modifieLe; }
    public function setModifieLe(\DateTimeImmutable $dt): static { $this->modifieLe = $dt; return $this; }
}
