<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Informations de l'entreprise utilisatrice (configuration centralisée, singleton).
 * Alimente l'en-tête des devis/factures et les mentions légales.
 */
#[ORM\Entity(repositoryClass: SocieteRepository::class)]
class Societe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $formeJuridique = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Assert\Regex(pattern: '/^[0-9]{14}$/', message: 'Le SIRET doit contenir 14 chiffres.')]
    private ?string $siret = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numTva = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pays = 'France';

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteWeb = null;

    /** Chemin du fichier logo (uploadé). */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $capital = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rcs = null;

    #[ORM\Column(length: 34, nullable: true)]
    private ?string $iban = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $bic = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $tauxTvaDefaut = '20.00';

    #[ORM\Column(length: 3)]
    private string $devise = 'EUR';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $mentionsLegales = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $conditionsPaiement = null;

    #[ORM\Column(length: 20)]
    private string $prefixeFacture = 'FAC-';

    #[ORM\Column]
    private int $prochainNumeroFacture = 1;

    public function getId(): ?int { return $this->id; }

    public function getRaisonSociale(): ?string { return $this->raisonSociale; }
    public function setRaisonSociale(string $v): static { $this->raisonSociale = $v; return $this; }

    public function getFormeJuridique(): ?string { return $this->formeJuridique; }
    public function setFormeJuridique(?string $v): static { $this->formeJuridique = $v; return $this; }

    public function getSiret(): ?string { return $this->siret; }
    public function setSiret(?string $v): static { $this->siret = $v; return $this; }

    public function getNumTva(): ?string { return $this->numTva; }
    public function setNumTva(?string $v): static { $this->numTva = $v; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $v): static { $this->adresse = $v; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(?string $v): static { $this->codePostal = $v; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $v): static { $this->ville = $v; return $this; }

    public function getPays(): ?string { return $this->pays; }
    public function setPays(?string $v): static { $this->pays = $v; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $v): static { $this->telephone = $v; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $v): static { $this->email = $v; return $this; }

    public function getSiteWeb(): ?string { return $this->siteWeb; }
    public function setSiteWeb(?string $v): static { $this->siteWeb = $v; return $this; }

    public function getLogo(): ?string { return $this->logo; }
    public function setLogo(?string $v): static { $this->logo = $v; return $this; }

    public function getCapital(): ?string { return $this->capital; }
    public function setCapital(?string $v): static { $this->capital = $v; return $this; }

    public function getRcs(): ?string { return $this->rcs; }
    public function setRcs(?string $v): static { $this->rcs = $v; return $this; }

    public function getIban(): ?string { return $this->iban; }
    public function setIban(?string $v): static { $this->iban = $v; return $this; }

    public function getBic(): ?string { return $this->bic; }
    public function setBic(?string $v): static { $this->bic = $v; return $this; }

    public function getTauxTvaDefaut(): ?string { return $this->tauxTvaDefaut; }
    public function setTauxTvaDefaut(?string $v): static { $this->tauxTvaDefaut = $v; return $this; }

    public function getDevise(): string { return $this->devise; }
    public function setDevise(string $v): static { $this->devise = $v; return $this; }

    public function getMentionsLegales(): ?string { return $this->mentionsLegales; }
    public function setMentionsLegales(?string $v): static { $this->mentionsLegales = $v; return $this; }

    public function getConditionsPaiement(): ?string { return $this->conditionsPaiement; }
    public function setConditionsPaiement(?string $v): static { $this->conditionsPaiement = $v; return $this; }

    public function getPrefixeFacture(): string { return $this->prefixeFacture; }
    public function setPrefixeFacture(string $v): static { $this->prefixeFacture = $v; return $this; }

    public function getProchainNumeroFacture(): int { return $this->prochainNumeroFacture; }
    public function setProchainNumeroFacture(int $v): static { $this->prochainNumeroFacture = $v; return $this; }

    public function __toString(): string
    {
        return $this->raisonSociale ?? 'Société';
    }
}
