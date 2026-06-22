<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomComplet = null;

    /**
     * Rôles "bruts" optionnels (ex. forcer ROLE_ADMIN). La sécurité réelle passe
     * surtout par les rôles-entités et leurs permissions (voir getRoles()).
     *
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $actif = true;

    /**
     * Rôles applicatifs (entités) attribués à l'utilisateur.
     *
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $rolesEntities;

    public function __construct()
    {
        $this->rolesEntities = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getNomComplet(): ?string { return $this->nomComplet; }
    public function setNomComplet(?string $nomComplet): static { $this->nomComplet = $nomComplet; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    /**
     * Construit la liste des attributs de sécurité Symfony :
     * - ROLE_USER (toujours)
     * - les rôles bruts éventuels
     * - ROLE_<CODE> pour chaque rôle-entité
     * - ROLE_<CODE_PERMISSION> pour chaque permission de ces rôles
     * - ROLE_ADMIN si l'utilisateur a le rôle super-admin
     *
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        foreach ($this->rolesEntities as $role) {
            $roles[] = 'ROLE_' . $role->getCode();
            if ($role->isAdmin()) {
                $roles[] = 'ROLE_ADMIN';
            }
            foreach ($role->getPermissions() as $permission) {
                $roles[] = 'ROLE_' . $permission->getCode();
            }
        }

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /** @see PasswordAuthenticatedUserInterface */
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    /** @return Collection<int, Role> */
    public function getRolesEntities(): Collection { return $this->rolesEntities; }

    public function addRolesEntity(Role $role): static
    {
        if (!$this->rolesEntities->contains($role)) {
            $this->rolesEntities->add($role);
        }
        return $this;
    }

    public function removeRolesEntity(Role $role): static
    {
        $this->rolesEntities->removeElement($role);
        return $this;
    }

    /**
     * Vrai si l'utilisateur possède un rôle super-admin.
     */
    public function estAdmin(): bool
    {
        foreach ($this->rolesEntities as $role) {
            if ($role->isAdmin()) {
                return true;
            }
        }
        return in_array('ROLE_ADMIN', $this->roles, true);
    }

    public function eraseCredentials(): void
    {
        // Aucune donnée sensible temporaire à effacer.
    }

    /**
     * Ne sérialise en session que les champs scalaires essentiels (le hash du
     * mot de passe est masqué). L'utilisateur complet est rechargé depuis la base
     * à chaque requête par le provider Doctrine.
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => hash('crc32c', (string) $this->password),
            'actif' => $this->actif,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->actif = $data['actif'] ?? true;
        $this->rolesEntities = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nomComplet ?: (string) $this->email;
    }
}
