<?php

namespace App\Enum;

enum TypeContrat: string
{
    case DEVIS = 'DEVIS';
    case CONTRAT = 'CONTRAT';

    public function libelle(): string
    {
        return match ($this) {
            self::DEVIS => 'Devis',
            self::CONTRAT => 'Contrat',
        };
    }
}
