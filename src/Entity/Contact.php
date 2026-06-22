<?php

namespace App\Entity;

use App\Enum\TypeContact;
use App\Repository\ContactRepository;
use App\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, enumType: TypeContact::class)]
    private TypeContact $type = TypeContact::PARTICULIER;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Assert\Regex(pattern: '/^[0-9]{14}$/', message: 'Le SIRET doit contenir 14 chiffres.')]
    private ?string $siret = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numTva = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pays = 'France';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    use TimestampableTrait;

    public function getId(): ?int { return $this->id; }

    public function getType(): TypeContact { return $this->type; }
    public function setType(TypeContact $type): static { $this->type = $type; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getSiret(): ?string { return $this->siret; }
    public function setSiret(?string $siret): static { $this->siret = $siret; return $this; }

    public function getNumTva(): ?string { return $this->numTva; }
    public function setNumTva(?string $numTva): static { $this->numTva = $numTva; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): static { $this->telephone = $telephone; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): static { $this->adresse = $adresse; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(?string $codePostal): static { $this->codePostal = $codePostal; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): static { $this->ville = $ville; return $this; }

    public function getPays(): ?string { return $this->pays; }
    public function setPays(?string $pays): static { $this->pays = $pays; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static { $this->notes = $notes; return $this; }

    public function __toString(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? '')) ?: 'Contact';
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        // SIRET obligatoire pour les entreprises et associations
        if (in_array($this->type, [TypeContact::ENTREPRISE, TypeContact::ASSOCIATION], true) && empty($this->siret)) {
            $context->buildViolation('Le SIRET est obligatoire pour une entreprise ou une association.')
                ->atPath('siret')
                ->addViolation();
        }
    }
}
