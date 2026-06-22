<?php

namespace App\Entity;

use App\Enum\CodeModule;
use App\Repository\ModuleRepository;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Module activable/désactivable. Permet de vendre l'ERP selon l'usage du client.
 */
#[ORM\Entity(repositoryClass: ModuleRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_MODULE_CODE', fields: ['code'])]
#[UniqueEntity(fields: ['code'], message: 'Ce module existe déjà.')]
class Module
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, enumType: CodeModule::class)]
    private CodeModule $code;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $actif = false;

    public function getId(): ?int { return $this->id; }

    public function getCode(): CodeModule { return $this->code; }
    public function setCode(CodeModule $code): static { $this->code = $code; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function __toString(): string
    {
        return $this->nom ?? $this->code->libelle();
    }
}
