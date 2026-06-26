<?php

namespace App\Enum;

enum ModePaiement: string
{
    case VIREMENT = 'VIREMENT';
    case CARTE = 'CARTE';
    case CHEQUE = 'CHEQUE';
    case ESPECES = 'ESPECES';
    case PRELEVEMENT = 'PRELEVEMENT';

    public function libelle(): string
    {
        return match ($this) {
            self::VIREMENT => 'Virement',
            self::CARTE => 'Carte bancaire',
            self::CHEQUE => 'Chèque',
            self::ESPECES => 'Espèces',
            self::PRELEVEMENT => 'Prélèvement',
        };
    }
}
