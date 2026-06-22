<?php

namespace App\Enum;

enum TypeContact: string
{
    case PARTICULIER = 'PARTICULIER';
    case ENTREPRISE = 'ENTREPRISE';
    case ASSOCIATION = 'ASSOCIATION';
    case FOURNISSEUR = 'FOURNISSEUR';
    case PRESTATAIRE = 'PRESTATAIRE';

    public function libelle(): string
    {
        return match ($this) {
            self::PARTICULIER => 'Particulier',
            self::ENTREPRISE => 'Entreprise',
            self::ASSOCIATION => 'Association',
            self::FOURNISSEUR => 'Fournisseur',
            self::PRESTATAIRE => 'Prestataire',
        };
    }
}
