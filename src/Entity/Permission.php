<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use App\Trait\SoftDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un droit unitaire (ex. CONTACT_VOIR, FACTURE_CREER).
 * Les permissions sont reliées aux rôles (Many-to-Many).
 */
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_PERMISSION_CODE', fields: ['code'])]
#[UniqueEntity(fields: ['code'], message: 'Ce code de permission existe déjà.')]
class Permission implements SoftDeletableInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Code technique, ex. CONTACT_VOIR. Sert d'attribut de sécurité (ROLE_<code>). */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[A-Z0-9_]+$/', message: 'Le code doit être en MAJUSCULES (lettres, chiffres, underscore).')]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $libelle = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $module = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getLibelle(): ?string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }

    public function getModule(): ?string { return $this->module; }
    public function setModule(?string $module): static { $this->module = $module; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    /** @return Collection<int, Role> */
    public function getRoles(): Collection { return $this->roles; }

    public function __toString(): string
    {
        return $this->libelle ?? $this->code ?? '';
    }
}
