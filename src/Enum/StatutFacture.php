<?php

namespace App\Enum;

enum StatutFacture: string
{
    case EN_ATTENTE = 'EN_ATTENTE';
    case ENVOYEE = 'ENVOYEE';
    case PAYEE = 'PAYEE';
    case IMPAYEE = 'IMPAYEE';
    case ANNULEE = 'ANNULEE';

    public function libelle(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::ENVOYEE => 'Envoyée',
            self::PAYEE => 'Payée',
            self::IMPAYEE => 'Impayée',
            self::ANNULEE => 'Annulée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'secondary',
            self::ENVOYEE => 'info',
            self::PAYEE => 'success',
            self::IMPAYEE => 'danger',
            self::ANNULEE => 'dark',
        };
    }
}
